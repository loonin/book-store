$(function () {
    // init masonry lib
    $('.grid').masonry({
        itemSelector: '.book',
        columnWidth: 330
    });

    // set calendar for datetime field
    $('#book_reading_date').datepicker({
        dateFormat: "dd.mm.yy",
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель',
            'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь',
            'Октябрь', 'Ноябрь', 'Декабрь'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        firstDay: 1
    });

    // set ajax
    $('.js-delete').on('click', function (e) {
        e.preventDefault();

        var url = this.href,
            objectType = $(this).data('object-type');

        $.ajax({
            url: url,
            success: function (data) {
                if (data.success) {
                    $('.book-form-row-' + objectType).remove();
                    $('#field-' + objectType).show();
                } else {
                    alert('Ошибка удаления обложки. Обратитесь к администратору.');
                }
            },
            dataType: 'json'
        });
    });
});