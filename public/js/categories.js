/**
 * A 'click' event for edit-category button
 */
$('.categoryButton').on('click', function() {
	setInputValue(this.value);
	setInputHiddenValue(this.value);
});

/**
 * Set value of edit-category text input
 * 
 * @param string  Category name
 * 
 * @return void
 */
function setInputValue(value) {
	$('.editCategoryInput').val(value);
}

/**
 * Set value of edit-category hidden text input
 * 
 * @param string  Category name
 * 
 * @return void
 */
function setInputHiddenValue(value) {
	$('.hiddenCategoryInput').val(value);
}
