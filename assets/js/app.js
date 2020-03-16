var BASE_URL = window.location.origin;

axios.defaults.headers.post['Content-Type'] = 'application/json';
axios.defaults.headers.post['X-Api-Key'] = '6Vlo7xk4Ih0tOD6zD4IgUjO07MxXJ8';

numeral.register('locale', 'id', {
    delimiters: {
        thousands: '.',
        decimal: '.'
    },
    abbreviations: {
        thousand: 'ribu',
        million: 'juta',
        billion: 'milyar',
        trillion: 'triliun'
    },
    ordinal : function (number) {
        return number === 1 ? '' : '';
    },
    currency: {
        symbol: 'Rp'
    }
});
numeral.locale('id');

function createURL(params) {
    params = params.replace(/\s/g, "-");
    params = params.replace(/[^A-Za-z0-9 \-]/g, "");
    return params.toLowerCase();
}

function getFormData(form, json = true){
    var unindexed_array = $(form).serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    if(json) return JSON.stringify(indexed_array);
    return indexed_array;
}