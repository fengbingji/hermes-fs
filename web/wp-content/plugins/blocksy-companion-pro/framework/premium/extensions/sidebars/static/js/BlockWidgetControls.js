import { createElement, Fragment } from '@wordpress/element'
import { __ } from 'ct-i18n'

import { SingleSidebarSettings } from './SettingsManager'
import { getLocalizations } from '../../../../../../static/js/editor/utils/localizations'

const BlockWidgetControls = ({ sidebarId }) => {
	return (
		<Fragment>
			<SingleSidebarSettings sidebarId={sidebarId} />

			<div
				className="ct-custom-sidebar-remove"
				data-tooltip-reveal="top"
				onClick={() => {
					wp.ajax
						.send({
							url: `${wp.ajax.settings.url}?action=blocksy_sidebars_remove&id=${sidebarId}&nonce=${getLocalizations().dashboard_actions_nonce}`,
							contentType: 'application/json'
						})
						.then(() => location.reload())
				}}
			>
				<span className="dashicons dashicons-no-alt"></span>
				<span className="ct-tooltip">
					{__('Remove Sidebar', 'blocksy-companion')}
				</span>
			</div>
		</Fragment>
	)
}

export default BlockWidgetControls
