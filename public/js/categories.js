/**
 * A 'click' event for edit-income-category button
 */
$('.incomeButton').on('click', function() {
	setIncomeValue(this.value);
	setIncomeHiddenValue(this.value);
});

/**
 * A 'click' event for delete-income-category button
 */
$('.incomeDeleteButton').on('click', function() {
	setDeleteIncomeHiddenValue(this.value);
});

/**
 * Set value of edit-income-category text input
 * 
 * @param string  Category name
 * 
 * @return void
 */
function setIncomeValue(value) {
	$('#editIncomeInput').val(value);
}

/**
 * Set value of edit-income-category hidden text input
 * 
 * @param string  Category name
 * 
 * @return void
 */
function setIncomeHiddenValue(value) {
	$('#hiddenIncomeInput').val(value);
}

/**
 * Set value of delete-income-category hidden text input
 * 
 * @param string  Category name
 * 
 * @return void
 */
function setDeleteIncomeHiddenValue(value) {
	$('#hiddenDeleteIncomeInput').val(value);
}
