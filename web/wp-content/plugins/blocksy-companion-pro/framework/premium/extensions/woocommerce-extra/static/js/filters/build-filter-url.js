const STRIPPED_PARAMS = ['paged', 'blocksy_ajax']

const readParams = (url) => {
	const params = new Map()

	url.searchParams.forEach((value, key) => {
		params.set(key, value)
	})

	return params
}

const finalize = ({ url, params }) => {
	STRIPPED_PARAMS.forEach((key) => params.delete(key))

	const sorted = new URLSearchParams()

	Array.from(params.keys())
		.sort()
		.forEach((key) => sorted.set(key, params.get(key)))

	const search = sorted.toString()

	return (
		url.origin +
		url.pathname.replace(/\/page\/[0-9]+/, '') +
		(search ? `?${search}` : '')
	)
}

export const buildFilterUrl = ({
	currentUrl,
	param,
	value,
	multiple = true,
	extra = ''
}) => {
	const url = new URL(currentUrl)
	const params = readParams(url)
	const extraPairs = Array.from(new URLSearchParams(extra).entries())

	const setExtra = () => {
		extraPairs.forEach(([key, extraValue]) => params.set(key, extraValue))
	}

	if (!params.has(param)) {
		params.set(param, value)
		setExtra()

		return finalize({ url, params })
	}

	let values = params.get(param).split(',')

	if (multiple) {
		values = values.includes(value)
			? values.filter((item) => item !== value)
			: [...values, value]
	} else {
		values = values.includes(value) ? [] : [value]
	}

	values.sort()

	if (values.length) {
		params.set(param, values.join(','))
		setExtra()

		return finalize({ url, params })
	}

	params.delete(param)
	extraPairs.forEach(([key]) => params.delete(key))

	return finalize({ url, params })
}

export const resetFilterUrl = ({ currentUrl, params }) => {
	const url = new URL(currentUrl)
	const current = readParams(url)

	params.forEach((key) => current.delete(key))

	return finalize({ url, params: current })
}
