/**
 * A 'change' event for edit-amount and select-category inputs
 * Displays limit-message-box depending on the expense-category and amount
 */
$('.limits').on('change', function() {
	var expense = getValues();
	
	$.post(
		'/Expense/getLimitAction',
		{expense})
		.fail(function() {
			$('.limit').css('display', 'none');
			})
		.done(function(data) {
			data = $.parseJSON(data);

			$('#limitCell').html(data.limit + ' zł');
			$('#sumCell').html(data.sum + ' zł');
			$('#leftCell').html(data.left + ' zł');
			
			if (data.left < 0) {
				$('.limitBox').addClass('alert-danger');
				$('.limitBox').removeClass('alert-success');
			} else {
				$('.limitBox').addClass('alert-success');
				$('.limitBox').removeClass('alert-danger');
			}
			
			$('.limit').css('display', 'flex');
		});
});

/**
 * Get values of amount and category for new-expense
 * 
 * @return mixed  The associative array of amount and category of new-expense if the date is correct, false otherwise
 */
function getValues() {
	var expense = {
	'category': $('.limitCategory').val(),
	'amount': $('.limitAmount').val(),
	'date': $('.limitDate').val()
	};

	if (isCorrectDate(expense['date'])) {
		return expense;
	}
}

/**
 * Check if date-input is not empty
 * 
 * @param string  The input's value
 * 
 * @return boolean  True if input is not empty, false otherwise
 */
function isCorrectDate(date) {
	if (date) {
		return true;
	}
	return false;
}
