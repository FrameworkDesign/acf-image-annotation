module.exports = {
    "extends": [
        "airbnb-base",
        "plugin:vue/essential"
    ],
    "env": {
        "node": true, // this is the best starting point
        "es6": true // enables es6 features
    },
    "plugins": [
        "html"
    ],
    "parserOptions": {
        "parser": "babel-eslint",
    },
    "rules": {
        "strict": 0,
        "no-console": "off",
        "no-unused-vars": 0,
        "indent": 0,
        "radix": "off",
        "max-len": "off",
        "no-param-reassign": "off",
        "eol-last": "off",
        "class-methods-use-this": "off",
        "no-trailing-spaces": "off",
        "padded-blocks": "off"
    },
    "globals": {
        "_": false,
        "window": true,
        "document": true,
        "Vue": true
    }
};