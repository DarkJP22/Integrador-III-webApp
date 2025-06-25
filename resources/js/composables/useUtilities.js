import { format, parseISO, formatISO } from 'date-fns';
import { readonly, shallowRef, watchEffect } from 'vue';


export default function(){

    const monthsLong = {
        1: 'January',
        2: 'February',
        3: 'March',
        4: 'April',
        5: 'May',
        6: 'June',
        7: 'July',
        8: 'August',
        9: 'September',
        10: 'October',
        11: 'November',
        12: 'December'
    };
    const parseDatetime = (date) =>{
        if(!date) return '';
        return format(parseISO(date), 'yyyy-MM-dd hh:mm a');
    };

    const parseIsoDatetime = (date) =>{
        if(!date) return '';
        return formatISO(parseISO(date));
    };

    const parseIsoDatetimeWithoutTz = (date, seconds = true) =>{
        if(!date) return '';
        return format(parseISO(date), seconds ? 'yyyy-MM-dd\'T\'HH:mm:00' : 'yyyy-MM-dd\'T\'HH:mm');
    };

    const parseDate = (date) =>{
        if(!date) return '';
        return format(parseISO(date), 'yyyy-MM-dd');
    };

    const parseTime12 = (date) =>{
        if(!date) return '';
        return format(parseISO(date), 'hh:mm:00 a');
    };
    const parseTime24 = (date, seconds = true) =>{
        if(!date) return '';
        return format(parseISO(date), seconds ? 'HH:mm:00' : 'HH:mm');
    };

    const moneyFormat = (value, currency) => {
        if(value === null || value === undefined) return;

        if (!currency) {
            currency = {
                precision: 2,
                thousand_separator: ',',
                decimal_separator: '.',
                symbol: '₡',
                code: 'CRC'
            };
        }

        const {
            precision,
            //symbol,
            code
        } = currency;

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: code,

            // These options are needed to round to whole numbers if that's what you want.
            minimumFractionDigits: precision, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
            maximumFractionDigits: precision, // (causes 2500.99 to be printed as $2,501)
        });

        if(typeof value === 'string'){

            value = parseFloat(value);
        }

        return formatter.format(value);


    };

    const eagerComputed = (fn) => {
        const result = shallowRef();
        watchEffect(() => {
            result.value = fn();
        },
        {
            flush: 'sync' // needed so updates are immediate.
        });

        return readonly(result);
    };

    const toFormData = (object) => {
        const formData = new FormData();

        Object.keys(object).forEach((key) => {
            if (Array.isArray(object[key])) {
                formData.append(key, JSON.stringify(object[key]));
            } else {
                // Convert null to empty strings (because formData does not support null values and converts it to string)
                if (object[key] === null) {
                    object[key] = '';
                }

                formData.append(key, object[key]);
            }
        });

        return formData;
    };





    return {
        parseDatetime,
        parseDate,
        parseTime12,
        parseTime24,
        parseIsoDatetime,
        parseIsoDatetimeWithoutTz,
        moneyFormat,
        monthsLong,
        eagerComputed,
        toFormData

    };

}
