/**
 * Utility function to trim html content into a specific length.
 *
 * @param {string} html
 * @param {number} length
 * @returns {string}
 */
listo.trimHtml = function (html, length) {
	/**
	 * Trim text content.
	 *
	 * @param {Element} element
	 * @param {number} length
	 * @returns {Object}
	 */
	function trimmer(element, length = 0) {
		[...element.childNodes].forEach(node => {
			// Skip unrelated nodetypes.
			// https://developer.mozilla.org/en-US/docs/Web/API/Node/nodeType
			if (![Node.ELEMENT_NODE, Node.TEXT_NODE].find(t => t === node.nodeType)) {
				return;
			}

			// Remove node when the maximum length is reached.
			if (length <= 0) {
				node.remove();
				return;
			}

			// On element node, recursively calls the trimmer function.
			if (Node.ELEMENT_NODE === node.nodeType) {
				// Handle empty element nodes correctly.
				// https://developer.mozilla.org/en-US/docs/Glossary/Empty_element
				if ('BR' === node.tagName) {
					length--;
					return;
				}

				let result = trimmer(node, length);

				// Update the length after trimmed.
				length = result.length;

				// Remove the element if it is empty after trimmed.
				if (!node.childNodes.length) {
					node.remove();
				}
			}

			// On text node, do text trimming if necessary.
			if (Node.TEXT_NODE === node.nodeType) {
				let text = node.textContent;

				// Just update the remaining allocated length if text's length is still shorter than that.
				if (text.length <= length) {
					length -= text.length;
				}
				// Otherwise, trim the text.
				else {
					// Safe to trim the text since the immediate character after trimmed length is a whitespace.
					if (text[length].match(/\s/)) {
						text = text.slice(0, length);
					}
					// Otherwise, we are likely to cut word during trimming. In this case, we should also remove
					// the last word being cut.
					else {
						text = text.slice(0, length);

						// Remove the last word being cut.
						let lastSpaceIndex = text.lastIndexOf(' ');
						if (lastSpaceIndex === -1) {
							text = '';
						} else {
							text = text.slice(0, Math.min(text.length, lastSpaceIndex));
						}
					}

					// Replace content with trimmed text.
					if (text.length) {
						node.textContent = text;
					} else {
						node.remove();
					}

					// Update remaining allocated length to 0 as we consider subsequent texts to be removed.
					length = 0;
				}
			}
		});

		return { element, length };
	}

	// Create a temporary HTML container.
	let element = document.createElement('div');
	element.innerHTML = html;

	let result = trimmer(element, length);

	return result.element.innerHTML;
};
