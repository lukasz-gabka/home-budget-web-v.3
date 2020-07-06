/**
 * A 'click' event for edit-income-category button
 */
$('.incomeButton').on('click', function() {
	setIncomeValue(this.value);
	setIncomeHiddenValue(this.value);
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
