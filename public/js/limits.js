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
 * @return array  The associative array of amount and category of new-expense
 */
function getValues() {
	var expense = {
	'category': $('.limitCategory').val(),
	'amount': $('.limitAmount').val()
	};

	return expense;
}
