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

/**
 * A 'checked' event for displaying expense-category-limit input
 */
$('#categoryLimit').on('click', function() {
	displayLimitInput();
});

/**
 * Display/hide expense-category-limit input by clicking the '#categoryLimit' checkbox
 * 
 * @return void
 */
function displayLimitInput() {
	if ($('#categoryLimit').prop('checked')) {
		$('#inputLimitDiv').css('display', 'flex');
		$('#inputLimit').prop('required', true);
	} else {
		$('#inputLimitDiv').hide();
		$('#inputLimit').prop('required', false);
		$('#inputLimit').val('');
	}
}
