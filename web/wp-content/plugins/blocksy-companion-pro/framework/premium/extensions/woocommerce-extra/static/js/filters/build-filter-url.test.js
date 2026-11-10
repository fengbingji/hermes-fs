import { buildFilterUrl, resetFilterUrl } from './build-filter-url'

import fixtures from '../../../../../../../tests/fixtures/filter-urls.json'

const normalize = (url) => {
	const parsed = new URL(url)
	const query = {}

	Array.from(parsed.searchParams.keys())
		.sort()
		.forEach((key) => {
			query[key] = parsed.searchParams.get(key)
		})

	return { path: decodeURIComponent(parsed.pathname), query }
}

const decodeStoredSlug = (value) =>
	decodeURIComponent(value.replace(/\+/g, ' '))

const run = (testCase) => {
	if (testCase.type === 'reset') {
		return resetFilterUrl({
			currentUrl: testCase.url,
			params: testCase.remove
		})
	}

	return buildFilterUrl({
		currentUrl: testCase.url,
		param: testCase.param,
		value: testCase.value_is_stored_slug
			? decodeStoredSlug(testCase.value)
			: testCase.value,
		multiple: testCase.is_multiple,
		extra: new URLSearchParams(testCase.to_add).toString()
	})
}

describe('filter URL fixtures', () => {
	test.each(fixtures.cases.map((testCase) => [testCase.name, testCase]))(
		'%s',
		(name, testCase) => {
			const expected = testCase.expect_js || testCase.expect

			expect(normalize(run(testCase))).toEqual(expected)
		}
	)
})

describe('buildFilterUrl output shape', () => {
	test('returns an absolute same-origin URL without a trailing question mark', () => {
		expect(
			buildFilterUrl({
				currentUrl: 'https://shop.test/shop/?filter_color=red',
				param: 'filter_color',
				value: 'red'
			})
		).toBe('https://shop.test/shop/')
	})

	test('a protocol-relative path cannot change the origin', () => {
		expect(
			buildFilterUrl({
				currentUrl: 'https://shop.test//evil.test/x',
				param: 'a',
				value: 'b'
			}).startsWith('https://shop.test/')
		).toBe(true)
	})
})
