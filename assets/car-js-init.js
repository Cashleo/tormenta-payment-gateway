$(function() {
    $("#card-number").change(function() {
        $("#card-type").val(CardJs.cardTypeFromNumber($(this).val()));

        var cleanCardNumber = CardJs.numbersOnlyString($(this).val());
        $(this).val(CardJs.applyFormatMask(cleanCardNumber, 'XXXX XXXX XXXX XXXX'));
    });
});