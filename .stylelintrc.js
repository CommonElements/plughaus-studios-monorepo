module.exports = {
  extends: [
    'stylelint-config-standard-scss'
  ],
  plugins: [
    'stylelint-scss'
  ],
  rules: {
    // Indentation
    'indentation': 2,
    
    // Color
    'color-hex-case': 'lower',
    'color-hex-length': 'short',
    'color-named': 'never',
    
    // Font family
    'font-family-name-quotes': 'always-where-recommended',
    
    // Function
    'function-comma-space-after': 'always-single-line',
    'function-parentheses-space-inside': 'never',
    'function-url-quotes': 'always',
    
    // Number
    'number-leading-zero': 'always',
    'number-no-trailing-zeros': true,
    
    // String
    'string-quotes': 'single',
    
    // Unit
    'unit-case': 'lower',
    
    // Value
    'value-keyword-case': 'lower',
    'value-list-comma-space-after': 'always-single-line',
    
    // Property
    'property-case': 'lower',
    
    // Declaration
    'declaration-bang-space-after': 'never',
    'declaration-bang-space-before': 'always',
    'declaration-colon-space-after': 'always-single-line',
    'declaration-colon-space-before': 'never',
    
    // Declaration block
    'declaration-block-semicolon-newline-after': 'always-multi-line',
    'declaration-block-semicolon-space-after': 'always-single-line',
    'declaration-block-semicolon-space-before': 'never',
    'declaration-block-trailing-semicolon': 'always',
    
    // Block
    'block-closing-brace-empty-line-before': 'never',
    'block-closing-brace-newline-after': 'always',
    'block-closing-brace-newline-before': 'always-multi-line',
    'block-opening-brace-newline-after': 'always-multi-line',
    'block-opening-brace-space-before': 'always',
    
    // Selector
    'selector-attribute-brackets-space-inside': 'never',
    'selector-attribute-quotes': 'always',
    'selector-combinator-space-after': 'always',
    'selector-combinator-space-before': 'always',
    'selector-descendant-combinator-no-non-space': true,
    'selector-pseudo-class-case': 'lower',
    'selector-pseudo-class-parentheses-space-inside': 'never',
    'selector-pseudo-element-case': 'lower',
    'selector-pseudo-element-colon-notation': 'double',
    'selector-type-case': 'lower',
    
    // Selector list
    'selector-list-comma-newline-after': 'always',
    'selector-list-comma-space-before': 'never',
    
    // Media feature
    'media-feature-colon-space-after': 'always',
    'media-feature-colon-space-before': 'never',
    'media-feature-parentheses-space-inside': 'never',
    'media-feature-range-operator-space-after': 'always',
    'media-feature-range-operator-space-before': 'always',
    
    // Media query list
    'media-query-list-comma-newline-after': 'always-multi-line',
    'media-query-list-comma-space-after': 'always-single-line',
    'media-query-list-comma-space-before': 'never',
    
    // At-rule
    'at-rule-case': 'lower',
    'at-rule-name-case': 'lower',
    'at-rule-name-space-after': 'always-single-line',
    'at-rule-semicolon-newline-after': 'always',
    
    // Comment
    'comment-whitespace-inside': 'always',
    
    // General / Sheet
    'max-empty-lines': 2,
    'no-eol-whitespace': true,
    'no-missing-end-of-source-newline': true,
    
    // SCSS specific rules
    'scss/at-extend-no-missing-placeholder': true,
    'scss/at-function-pattern': '^[a-z]+([a-z0-9-]+[a-z0-9]+)?$',
    'scss/at-import-no-partial-leading-underscore': true,
    'scss/at-import-partial-extension-blacklist': ['scss'],
    'scss/at-mixin-pattern': '^[a-z]+([a-z0-9-]+[a-z0-9]+)?$',
    'scss/at-rule-no-unknown': true,
    'scss/dollar-variable-colon-space-after': 'always',
    'scss/dollar-variable-colon-space-before': 'never',
    'scss/dollar-variable-pattern': '^[a-z]+([a-z0-9-]+[a-z0-9]+)?$',
    'scss/percent-placeholder-pattern': '^[a-z]+([a-z0-9-]+[a-z0-9]+)?$',
    'scss/selector-no-redundant-nesting-selector': true,
    
    // Disable some rules that conflict with our conventions
    'no-descending-specificity': null,
    'selector-class-pattern': null,
    'custom-property-pattern': null
  }
};