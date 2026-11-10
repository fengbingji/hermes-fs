import { buildFilterUrl, resetFilterUrl } from './build-filter-url'

const RATING_PARAM = 'filter_by_rating'

export const IGNORED_TARGETS = '.ct-expandable-trigger'

export const getControlUrl = (control) => {
	const { filterReset, filterValue } = control.dataset

	if (filterReset !== undefined) {
		return resetFilterUrl({
			currentUrl: window.location.href,
			params: filterReset.split(',').filter(Boolean)
		})
	}

	if (filterValue === undefined) {
		return null
	}

	const widget = control.closest('.ct-filter-widget')
	const source = control.dataset.filterParam ? control : widget

	if (!source || !source.dataset.filterParam) {
		return null
	}

	return buildFilterUrl({
		currentUrl: window.location.href,
		param: source.dataset.filterParam,
		value: filterValue,
		multiple: source.dataset.filterMultiple !== 'no',
		extra: source.dataset.filterExtra || ''
	})
}

const setState = (control, active) => {
	const item = control.closest('.ct-filter-item')
	const widget = control.closest('.ct-filter-widget')
	const checkbox = control.querySelector('input.ct-checkbox')

	control.setAttribute('aria-checked', active ? 'true' : 'false')

	if (item) {
		item.classList.toggle('active', active)
	}

	if (!widget || widget.dataset.filterParam !== RATING_PARAM) {
		control.classList.toggle('active', active)
	}

	if (checkbox) {
		checkbox.checked = active
	}
}

export const toggleControlState = (control) => {
	if (!control.hasAttribute('aria-checked')) {
		return
	}

	const widget = control.closest('.ct-filter-widget')
	const next = control.getAttribute('aria-checked') !== 'true'

	if (widget && widget.dataset.filterMultiple === 'no') {
		widget
			.querySelectorAll('.ct-filter-control[aria-checked]')
			.forEach((sibling) => {
				if (sibling !== control) {
					setState(sibling, false)
				}
			})
	}

	setState(control, next)
}
