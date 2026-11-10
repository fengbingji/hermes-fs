import { useEffect, useSyncExternalStore } from '@wordpress/element'
import cachedFetch from '@creative-themes/wordpress-helpers/cached-fetch'

let sidebars = []
const listeners = new Set()

const emit = () => {
	listeners.forEach((listener) => listener())
}

const subscribe = (listener) => {
	listeners.add(listener)

	return () => {
		listeners.delete(listener)
	}
}

export const updateSidebar = (sidebar) => {
	sidebars = sidebars.map((s) => {
		if (s.id !== sidebar.id) {
			return s
		}

		return sidebar
	})

	emit()
}

let isLoaded = false

const loadSidebars = () => {
	if (isLoaded) {
		return
	}

	isLoaded = true

	cachedFetch(`${ct_localizations.ajax_url}?action=blocksy_sidebars_list`, {})
		.then((response) => response.json())
		.then(({ success, data }) => {
			if (!success || !Array.isArray(data?.sidebars)) {
				throw new Error()
			}

			sidebars = data.sidebars
			emit()
		})
		.catch(() => {
			isLoaded = false
		})
}

export const useSidebar = (id) => {
	useEffect(() => {
		loadSidebars()
	}, [])

	return useSyncExternalStore(subscribe, () =>
		sidebars.find((sidebar) => sidebar.id === id)
	)
}
