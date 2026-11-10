import { registerDynamicChunk } from 'blocksy-frontend'

registerDynamicChunk('blocksy_ext_woo_extra_advanced_reviews_sync_cache', {
	mount: (el) => {
		const commentsEls = el.querySelectorAll('.ct-review-vote')

		if (!commentsEls.length) {
			return
		}

		const comments_ids = [
			...new Set(
				Array.from(commentsEls).map((el) => +el.dataset.commentId)
			)
		]

		fetch(`${ct_localizations.ajax_url}?action=ct_sync_votes`, {
			method: 'POST',
			body: JSON.stringify({
				comments_ids
			}),
			headers: {
				Accept: 'application/json',
				'Content-Type': 'application/json'
			}
		})
			.then((response) => response.json())
			.then(({ votes: allVotes }) => {
				if (!allVotes) {
					return
				}

				Object.entries(allVotes).forEach(([id, commentVotes]) => {
					if (!commentVotes) {
						return
					}

					const commentElUp = document.querySelector(
						`.ct-review-vote[data-comment-id="${id}"][data-vote="up"]`
					)

					const commentElDown = document.querySelector(
						`.ct-review-vote[data-comment-id="${id}"][data-vote="down"]`
					)

					if (!commentElUp || !commentElDown) {
						return
					}

					const { up = 0, down = 0, current = '' } = commentVotes

					commentElUp.dataset.buttonState =
						current === 'up' ? 'active' : ''
					commentElDown.dataset.buttonState =
						current === 'down' ? 'active' : ''

					const wrapper = commentElUp.closest('.ct-review-votes')

					const totalEl = wrapper.querySelector(
						'.ct-review-total-count'
					)

					const upvotesEl = wrapper.querySelector(
						'.ct-review-upvote-count'
					)
					const messageEl = wrapper.querySelector(
						'.ct-review-vote-count'
					)

					messageEl.dataset.count = up
					totalEl.innerHTML = up + down
					upvotesEl.innerHTML = up
				})
			})
	}
})
