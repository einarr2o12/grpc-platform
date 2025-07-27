export default [
  {
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: "commonjs",
      globals: {
        require: "readonly",
        module: "readonly",
        __dirname: "readonly",
        process: "readonly",
        console: "readonly",
        Buffer: "readonly",
        global: "readonly"
      }
    },
    rules: {
      "no-unused-vars": "warn",
      "no-console": "off",
      "semi": ["error", "always"],
      "quotes": ["error", "single"],
      "no-trailing-spaces": "error",
      "eol-last": "error",
      "indent": ["error", 2]
    },
    ignores: [
      "node_modules/**",
      "coverage/**",
      "*.min.js"
    ]
  }
];