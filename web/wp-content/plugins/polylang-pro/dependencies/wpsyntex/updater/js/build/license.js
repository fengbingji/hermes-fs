const pllSettingsLicenses = {
	/**
	 * Init.
	 */
	init: () => {
		if ( document.readyState !== 'loading' ) {
			pllSettingsLicenses.ready();
		} else {
			document.addEventListener( 'DOMContentLoaded', pllSettingsLicenses.ready );
		}
	},
	ready: () => {
		pllSettingsLicenses.manageLicenses.attachEvents();
		pllSettingsLicenses.manageLicenses.display();
	},

	manageLicenses: {
		/**
		 * Renders the server-provided table rows, then refreshes the buttons.
		 *
		 * @param {string[]} rows The row HTML fragments.
		 */
		renderTable: rows => {
			pllSettingsLicenses.manageLicenses.clearNotice();
			const table = document.getElementById( 'pllu-licenses-table' );
			if ( table ) {
				// Rows are escaped server-side by the PHP renderer.
				table.innerHTML = rows.join( '' );
			}
			pllSettingsLicenses.manageLicenses.refreshButtons();
		},

		/**
		 * Shows a generic error notice above the table (message localized in the view's data-pllu-error).
		 */
		notify: () => {
			const table = document.getElementById( 'pllu-licenses-table' );
			const message = table ? table.getAttribute( 'data-pllu-error' ) : '';
			if ( ! table || ! message ) {
				return;
			}

			let notice = document.getElementById( 'pllu-licenses-notice' );
			if ( ! notice ) {
				notice = document.createElement( 'div' );
				notice.id = 'pllu-licenses-notice';
				notice.className = 'notice notice-error';
				notice.innerHTML = '<p></p>';
				table.parentNode.insertBefore( notice, table );
			}
			notice.querySelector( 'p' ).textContent = message;
		},

		/**
		 * Removes the error notice, if any.
		 */
		clearNotice: () => {
			const notice = document.getElementById( 'pllu-licenses-notice' );
			if ( notice ) {
				notice.remove();
			}
		},

		/**
		 * Fetches and renders the whole licenses table.
		 */
		display: () => {
			const manager = pllSettingsLicenses.manageLicenses;
			const table = document.getElementById( 'pllu-licenses-table' );
			if ( ! table ) {
				return;
			}
			const action = table.getAttribute( 'data-action' );
			const nonce = table.getAttribute( 'data-nonce' );
			if ( ! action || ! nonce ) {
				return;
			}

			const url = wp.url.addQueryArgs( ajaxurl, {
				action,
				_pll_nonce: nonce,
				pll_ajax_settings: 1,
			} );

			fetch( url )
				.then( response => response.json() )
				.then( json => {
					if ( ! json.success ) {
						manager.notify();
						return;
					}

					manager.renderTable( json.data.row );
				} )
				.catch( () => manager.notify() )
				.finally( () => {
					const loading = table.querySelector( '.pllu-licenses-loading' );
					if ( loading ) {
						loading.remove();
					}
					table.removeAttribute( 'aria-busy' );
				} );
		},

		/**
		 * Attaches, once, the delegated listeners that survive table rebuilds.
		 */
		attachEvents: () => {
			const table = document.getElementById( 'pllu-licenses-table' );
			if ( ! table ) {
				return;
			}

			// Activate / Deactivate clicks.
			table.addEventListener( 'click', event => {
				if ( event.target.classList.contains( 'pllu-activate-license' ) ) {
					pllSettingsLicenses.manageLicenses.request( 'pllu_activate_license', event.target );
				} else if ( event.target.classList.contains( 'pllu-deactivate-license' ) ) {
					pllSettingsLicenses.manageLicenses.request( 'pllu_deactivate_license', event.target );
				}
			} );

			// Enable/disable the Activate button as the user types a key.
			table.addEventListener( 'input', event => {
				if ( event.target.tagName === 'INPUT' ) {
					pllSettingsLicenses.manageLicenses.refreshButtons();
				}
			} );
		},

		/**
		 * Disables every Activate button whose field is empty.
		 */
		refreshButtons: () => {
			const buttons = document.querySelectorAll( '#pllu-licenses-table .pllu-activate-license' );
			buttons.forEach( button => {
				const row = button.closest( 'tr' );
				const input = row ? row.querySelector( 'input' ) : null;
				button.disabled = ! input || '' === input.value.trim();
			} );
		},

		/**
		 * Sets the loading state while activating or deactivating a license.
		 *
		 * @param {Element|null} button The clicked button, or null when clearing the state.
		 * @param {boolean}      isBusy Whether a license request is running.
		 */
		setBusy: ( button, isBusy ) => {
			const table = document.getElementById( 'pllu-licenses-table' );
			if ( ! table ) {
				return;
			}

			if ( isBusy ) {
				table.setAttribute( 'aria-busy', 'true' );
			} else {
				table.removeAttribute( 'aria-busy' );
			}

			table.querySelectorAll( 'input, button' ).forEach( element => {
				element.disabled = isBusy;
			} );

			const activeSpinner = table.querySelector( '.pllu-license-spinner.is-active' );
			if ( activeSpinner ) {
				activeSpinner.classList.remove( 'is-active' );
			}

			if ( ! isBusy ) {
				pllSettingsLicenses.manageLicenses.refreshButtons();
				return;
			}

			const spinner = button.nextElementSibling;
			if ( spinner && spinner.classList.contains( 'pllu-license-spinner' ) ) {
				spinner.classList.add( 'is-active' );
			}
		},

		/**
		 * Sends an activate/deactivate request for the clicked button and applies the response.
		 *
		 * @param {string}  action The ajax action.
		 * @param {Element} button The clicked button.
		 */
		request: ( action, button ) => {
			if ( button.disabled ) {
				return;
			}

			const manager = pllSettingsLicenses.manageLicenses;
			const row = button.closest( 'tr' );
			const input = row ? row.querySelector( 'input' ) : null;
			const table = button.closest( '#pllu-licenses-table' );
			const nonce = table ? table.getAttribute( 'data-nonce' ) : '';

			if ( ! nonce ) {
				manager.notify();
				return;
			}

			const data = new FormData();
			data.set( 'action', action );
			data.set( 'pll_ajax_settings', 1 );
			data.set( 'id', button.id );
			data.set( '_pll_nonce', nonce );
			if ( input ) {
				data.set( 'key', input.value );
			}

			manager.setBusy( button, true );

			fetch( ajaxurl, { method: 'POST', body: data } )
				.then( response => response.json() )
				.then( json => {
					// Validation errors carry row HTML so they can still be displayed inline.
					if ( ! json.success && ! json.data?.html && ! json.data?.row ) {
						manager.notify();
						return;
					}

					// No single row to swap (single-field action): the response carries the rebuilt table.
					if ( ! json.data.html ) {
						manager.renderTable( json.data.row );
						return;
					}

					// Rows are escaped server-side by the PHP renderer.
					const license = document.querySelector( '#pllu-license-' + json.data.id );
					if ( license ) {
						license.outerHTML = json.data.html;
					}
					manager.clearNotice();
				} )
				.catch( () => manager.notify() )
				.finally( () => manager.setBusy( null, false ) );
		},
	},
};

pllSettingsLicenses.init();

