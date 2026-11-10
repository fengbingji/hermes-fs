jest.mock('@wordpress/element', () => ({
	useEffect: jest.fn(),
	useSyncExternalStore: jest.fn()
}))

jest.mock('@creative-themes/wordpress-helpers/cached-fetch', () => jest.fn())

const flushRequests = () => new Promise(setImmediate)

describe('shared sidebar list', () => {
	let cachedFetch
	let updateSidebar
	let mount
	let respond

	beforeEach(() => {
		jest.resetModules()
		global.ct_localizations = { ajax_url: '/wp-admin/admin-ajax.php' }
		cachedFetch = require('@creative-themes/wordpress-helpers/cached-fetch')
		cachedFetch.mockImplementation(
			() =>
				new Promise((resolve) => {
					respond = (sidebars) =>
						resolve({
							json: async () => ({
								success: true,
								data: { sidebars }
							})
						})
				})
		)

		const {
			useEffect,
			useSyncExternalStore
		} = require('@wordpress/element')
		const sidebarModule = require('./use-sidebars')
		updateSidebar = sidebarModule.updateSidebar

		mount = (id) => {
			const update = jest.fn()
			let unmount
			useSyncExternalStore.mockImplementation(
				(subscribe, getSnapshot) => {
					unmount = subscribe(() => update(getSnapshot()))
					return getSnapshot()
				}
			)
			useEffect.mockImplementation((effect) => effect())
			const initial = sidebarModule.useSidebar(id)
			return { initial, update, unmount }
		}
	})

	afterEach(() => {
		delete global.ct_localizations
	})

	test('50 widget areas share one initial request', async () => {
		const sidebars = Array.from({ length: 50 }, (_, index) => ({
			id: String(index),
			conditions: []
		}))
		const areas = sidebars.map(({ id }) => mount(id))
		expect(cachedFetch).toHaveBeenCalledTimes(1)
		expect(cachedFetch).toHaveBeenCalledWith(
			'/wp-admin/admin-ajax.php?action=blocksy_sidebars_list',
			{}
		)

		respond(sidebars)
		await flushRequests()
		areas.forEach(({ initial, update }, index) => {
			expect(initial).toBeUndefined()
			expect(update).toHaveBeenCalledTimes(1)
			expect(update.mock.lastCall[0]).toBe(sidebars[index])
		})
	})

	test('a saved sidebar notifies subscribers and replaces only its matching entry without refetching', async () => {
		const one = mount('one')
		const two = mount('two')
		const sidebars = [{ id: 'one' }, { id: 'two' }]
		respond(sidebars)
		await flushRequests()
		one.update.mockClear()
		two.update.mockClear()

		const saved = { id: 'one', conditions: [{ rule: 'everywhere' }] }
		updateSidebar(saved)

		expect(one.update).toHaveBeenCalledTimes(1)
		expect(one.update.mock.lastCall[0]).toBe(saved)
		expect(two.update).toHaveBeenCalledTimes(1)
		expect(two.update.mock.lastCall[0]).toBe(sidebars[1])
		expect(cachedFetch).toHaveBeenCalledTimes(1)
	})

	test('remounting preserves saved data instead of replaying the cached response', async () => {
		const removed = mount('one')
		respond([{ id: 'one', conditions: [] }])
		await flushRequests()
		removed.unmount()
		removed.update.mockClear()

		const saved = { id: 'one', conditions: [{ rule: 'everywhere' }] }
		updateSidebar(saved)
		const remounted = mount('one')
		await flushRequests()

		expect(removed.update).not.toHaveBeenCalled()
		expect(remounted.initial).toBe(saved)
		expect(remounted.update).not.toHaveBeenCalled()
		expect(cachedFetch).toHaveBeenCalledTimes(1)
	})
})
