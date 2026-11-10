import {
	createElement,
	createPortal,
	Fragment,
	useRef,
	useState
} from '@wordpress/element'
import { Overlay, OptionsPanel } from 'blocksy-options'
import { __, sprintf } from 'ct-i18n'

import { getLocalizations } from '../../../../../../static/js/editor/utils/localizations'
import { updateSidebar, useSidebar } from './use-sidebars'

export const SingleSidebarSettings = ({ sidebarId }) => {
	const sidebar = useSidebar(sidebarId)
	const [isEditing, setIsEditing] = useState(false)
	const [localSidebar, setLocalSidebar] = useState(null)

	const containerRef = useRef()

	const saveSettings = () => {
		wp.ajax
			.send({
				url: `${wp.ajax.settings.url}?action=blocksy_sidebars_update&nonce=${getLocalizations().dashboard_actions_nonce}`,
				contentType: 'application/json',
				data: JSON.stringify(localSidebar)
			})
			.then(() => {
				updateSidebar(localSidebar)
				setIsEditing(false)
			})
	}

	if (!sidebar) {
		return null
	}

	return (
		<Fragment>
			<div
				className="ct-custom-sidebar-settings"
				data-tooltip-reveal="top"
				onClick={() => {
					setLocalSidebar(null)
					setIsEditing(true)
				}}
			>
				<span className="dashicons dashicons-admin-generic"></span>
				<span className="ct-tooltip">
					{__('Display Conditions', 'blocksy-companion')}
				</span>
			</div>

			<Overlay
				items={isEditing}
				className="ct-admin-modal ct-custom-sidebars-modal"
				initialFocusRef={containerRef}
				onDismiss={() => {
					setIsEditing(false)
				}}
				render={() => (
					<div className="ct-modal-content" ref={containerRef}>
						<h2>
							{sprintf(
								__(
									'%s - Sidebar Display Conditions',
									'blocksy-companion'
								),
								sidebar.name
							)}
						</h2>
						<p>
							{__(
								'Add one or more conditions in order to display your sidebar.',
								'blocksy-companion'
							)}
						</p>

						<div className="ct-modal-scroll">
							<OptionsPanel
								onChange={(optionId, conditions) =>
									setLocalSidebar({
										...sidebar,
										...(localSidebar || {}),
										conditions
									})
								}
								options={{
									conditions: {
										type: 'blocksy-display-condition',
										value: [],
										label: false,
										design: 'none'
									}
								}}
								value={{
									conditions:
										(localSidebar
											? localSidebar.conditions
											: sidebar.conditions) || []
								}}
								hasRevertButton={false}
							/>
						</div>

						<div className="ct-modal-actions has-divider">
							<button
								className="ct-ui-button ct-ui-button-primary"
								disabled={!localSidebar}
								onClick={saveSettings}
							>
								{__('Save Settings', 'blocksy-companion')}
							</button>
						</div>
					</div>
				)}
			/>
		</Fragment>
	)
}

const SettingsManager = () => {
	const allElls = Array.from(
		document.querySelectorAll(
			'[id*="ct-dynamic-sidebar"] .blocksy-settings'
		)
	)

	return (
		<div>
			{allElls.map((el) => {
				const sidebarId = el.parentNode.id.replace(
					'ct-dynamic-sidebar-',
					''
				)

				return createPortal(
					<SingleSidebarSettings sidebarId={sidebarId} />,
					el,
					sidebarId
				)
			})}
		</div>
	)
}

export default SettingsManager
