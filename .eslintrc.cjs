module.exports = {
    'env': {
        'browser': true,
        'es2021': true,
        'commonjs': true,
        'vue/setup-compiler-macros': true
    },
    'globals': {
        'axios': 'readonly',
        'flash': 'readonly',
        '_': 'readonly',
        '$':'readonly',
        'redondeo': 'readonly',
        'Swal': 'readonly',
        'moment': 'readonly',
        'Vue': 'readonly'
    },
    'extends': [
        'eslint:recommended',
        'plugin:vue/vue3-essential'
    ],
    'overrides': [
    ],
    'parserOptions': {
        'ecmaVersion': 'latest',
        'sourceType': 'module'
    },
    'plugins': [
        'vue'
    ],
    'rules': {
        'indent': [
            'warn',
            4
        ],
        // "no-undef": ["error"],
        // "no-unused-vars": ["error"],
        'prefer-const': ['warn'],
        'quotes': [
            'warn',
            'single'
        ],
        'semi': [
            'warn',
            'always'
        ],
        'vue/multi-word-component-names': 'off',
    }
};
