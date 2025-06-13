module.exports = {
  root: true,
  env: {
    browser: true,
    es6: true,
    node: true,
    jest: true,
  },
  extends: [
    '@wordpress/eslint-config',
    '@wordpress/eslint-config/recommended',
  ],
  parser: '@typescript-eslint/parser',
  parserOptions: {
    ecmaVersion: 2022,
    sourceType: 'module',
    ecmaFeatures: {
      jsx: true,
    },
  },
  plugins: [
    '@typescript-eslint',
    'vue',
  ],
  overrides: [
    {
      files: ['*.ts', '*.tsx'],
      extends: [
        '@wordpress/eslint-config/recommended-with-formatting',
        '@typescript-eslint/eslint-plugin',
      ],
      rules: {
        '@typescript-eslint/no-unused-vars': 'error',
        '@typescript-eslint/explicit-function-return-type': 'warn',
      },
    },
    {
      files: ['*.vue'],
      extends: [
        'plugin:vue/vue3-recommended',
      ],
      rules: {
        'vue/multi-word-component-names': 'off',
      },
    },
    {
      files: ['*.php'],
      rules: {
        // PHP files handled by PHPCS
      },
    },
  ],
  globals: {
    wp: 'readonly',
    jQuery: 'readonly',
    $: 'readonly',
    ajaxurl: 'readonly',
    wpApiSettings: 'readonly',
  },
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'import/no-unresolved': 'off',
    'import/no-extraneous-dependencies': 'off',
  },
};