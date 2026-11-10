import { registerDynamicChunk } from 'blocksy-frontend'
import ctEvents from 'ct-events'

import { patchCurrentPageWith } from './filters/patch-current-page'
import { scrollToTarget } from '../../../../../../static/js/helpers/scroll-to-target'
import { updateQueryParams } from './filters/update-query-params'
import {
	IGNORED_TARGETS,
	getControlUrl,
	toggleControlState
} from './filters/navigation'
import { whenTransitionEnds } from '../../../../../../static/js/helpers/when-transition-ends'

const store = {}

const makeSilentRedirect = (url) => {
	const newUrl = new URL(url)

	newUrl.searchParams.delete('blocksy_ajax')

	window.history.replaceState(null, '', newUrl.toString())
}

const withAjaxParam = (url) => {
	const newUrl = new URL(url, window.location.href)

	newUrl.searchParams.set('blocksy_ajax', 'yes')

	return newUrl.toString()
}

export const cachedFetch = (originalUrl, silent_redirect = false) => {
	const url = withAjaxParam(originalUrl)

	return store[url]
		? new Promise((resolve) => {
				resolve(store[url])

				const isSilent = store[url].silent_redirect

				if (isSilent) {
					makeSilentRedirect(store[url].url)
				}

				if (!window.ct_customizer_localizations) {
					store[url] = store[url].clone()
					store[url].silent_redirect = isSilent
				}
			})
		: new Promise((resolve) =>
				fetch(url).then((response) => {
					if (response.redirected) {
						if (silent_redirect) {
							makeSilentRedirect(response.url)
						} else {
							const newUrl = new URL(response.url)

							newUrl.searchParams.delete('blocksy_ajax')

							window.location.replace(newUrl.toString())

							return
						}
					}

					resolve(response)

					if (!window.ct_customizer_localizations) {
						store[url] = response.clone()
						store[url].silent_redirect = silent_redirect
					}
				})
			)
}

export const beforeRequest = (fromCache) => {
	return new Promise((resolve) => {
		let products = document.querySelector('.ct-products-container')

		if (
			document.querySelector('[data-ajax-filters*="scroll"]') &&
			products &&
			products.closest('.ct-container') &&
			products.closest('.ct-container').getBoundingClientRect().top < 0
		) {
			scrollToTarget(products.closest('.ct-container'))
		}

		const loading = document.querySelector('.ct-filters-loading')

		if (!fromCache && loading) {
			loading.classList.add('active')
		}

		products.dataset.animate = 'leave:start'

		requestAnimationFrame(() => {
			products.dataset.animate = 'leave:end'

			const itemWithTransition = [...products.children].find((c) =>
				c.matches('[data-products], .woocommerce-no-products-found')
			)

			whenTransitionEnds(itemWithTransition, () => {
				products.dataset.animate = 'leave'
				resolve()
			})
		})

		const panel = document.querySelector('#woo-filters-panel')

		if (
			panel &&
			'closeOnSelect' in panel.dataset &&
			panel.classList.contains('active')
		) {
			const toggleEl = document.querySelector(
				'[data-toggle-panel="#woo-filters-panel"]'
			)

			if (toggleEl) {
				toggleEl.click()
			}
		}
	})
}

export const afterRequest = () => {
	const loading = document.querySelector('.ct-filters-loading')

	if (!loading) {
		return
	}

	const mount = () => {
		let products = document.querySelector('.ct-products-container')

		products.dataset.animate = 'appear:start'

		requestAnimationFrame(() => {
			products.dataset.animate = 'appear:end'

			const itemWithTransition = [...products.children].find((c) =>
				c.matches('[data-products], .woocommerce-no-products-found')
			)

			whenTransitionEnds(itemWithTransition, () => {
				products.removeAttribute('data-animate')
			})
		})

		ctEvents.trigger('blocksy:frontend:init')
	}

	if (loading.classList.contains('active')) {
		loading.classList.remove('active')

		whenTransitionEnds(loading, () => {
			mount()
		})
	} else {
		mount()
	}
}

export const fetchData = (uri, silent_redirect) =>
	new Promise((resolve) => {
		cachedFetch(uri, silent_redirect)
			.then((res) => res.text())
			.then((data, ...a) => {
				patchCurrentPageWith(data)
				resolve()
			})
	})

export const fetchDataFor = (url, silent_redirect) => {
	const fromCache = !!store[withAjaxParam(url)]

	beforeRequest(fromCache).then(() => {
		fetchData(url, silent_redirect).then(() => {
			setTimeout(() => {
				afterRequest()
			}, 50)
		})
	})
}

const hasModifier = (event) => event.metaKey || event.ctrlKey || event.shiftKey

registerDynamicChunk('blocksy_ext_woo_extra_ajax_filters', {
	mount: (el, { event }) => {
		if (event.type === 'popstate') {
			fetchDataFor(window.location.href)
			return
		}

		const isAjax = document.querySelector('[data-ajax-filters*="yes"]')

		if (
			el.matches(
				'.ct-filter-widget a, .ct-active-filters a, .ct-filter-reset a'
			)
		) {
			const url = getControlUrl(el)
			const ignored = event.target.closest(IGNORED_TARGETS)

			if (!url || (ignored && el.contains(ignored))) {
				return
			}

			if (event.type === 'keydown') {
				if (event.key !== 'Enter' && event.key !== ' ') {
					return
				}

				el.ctFilterKeyActivatedAt = event.timeStamp

				if (event.repeat) {
					event.preventDefault()
					return
				}
			}

			if (
				event.type === 'click' &&
				event.timeStamp - el.ctFilterKeyActivatedAt < 1000
			) {
				event.preventDefault()
				return
			}

			event.preventDefault()

			if (hasModifier(event)) {
				window.open(url, '_blank', 'noopener')
				return
			}

			toggleControlState(el)

			if (!isAjax) {
				window.location.assign(url)
				return
			}

			updateQueryParams(url)
			fetchDataFor(url)

			return
		}

		if (el.tagName === 'FORM') {
			return
		}

		if (!isAjax) {
			return
		}

		if (el.tagName === 'SELECT' && el.closest('.woocommerce-ordering')) {
			const url = new URL(window.location.href)
			const formData = new FormData(el.closest('.woocommerce-ordering'))

			;[...formData.entries()].map(([key, value]) => {
				if (key !== 'paged') {
					url.searchParams.set(key, value)
				}
			})

			const requestUrl = url.toString()

			updateQueryParams(requestUrl)
			fetchDataFor(requestUrl)

			return
		}

		const requestUrl = el.getAttribute('href')
		let silent_redirect = false

		if (el.classList.contains('page-numbers')) {
			silent_redirect = true
		}

		updateQueryParams(requestUrl)
		fetchDataFor(requestUrl, silent_redirect)
	}
})
