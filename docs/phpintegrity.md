# Php sniffer doc

To ensure the integrity and quality of your code, you can use PHP CodeSniffer. Follow the project installation steps to use the following commands:

## Linting Commands

- **Lint a single file:**

  ```sh
  npm run lint:php -- <path-of-your-file>
  ```

  Example:

  ```sh
  npm run lint:php -- ./src/Controllers/DatabaseConnector.php
  ```

- **Sniff the entire `src` directory:**

  ```sh
  npm run sniff:src
  ```

  This command will check all PHP files in the `src` directory and log any errors.

- **Sniff the `Controllers` directory:**

  ```sh
  npm run sniff:controllers
  ```

- **Sniff the `Models` directory:**

  ```sh
  npm run sniff:models
  ```

- **Sniff the `Views` directory:**

  ```sh
  npm run sniff:views
  ```

- **Sniff the `_partials` directory:**
  ```sh
  npm run sniff:partials
  ```

Your files should be error-free before creating a Pull Request. Make sure to verify them before asking for a review.
