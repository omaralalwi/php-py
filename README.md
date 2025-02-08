# PhpPy - PHP Python 🚀🐍
Seamlessly enabling secure and efficient execution of Python scripts within PHP applications without spread multiple applications and or setup  API.

## 📌 Table of Contents

- [📖 Overview](#-overview)
- [🔧 Requirements](#requirements)
- [🚀 Installation](#installation-)
- [🚀 Quick Start](#-quick-start)
- [✨ Features Summary](#-features-summary)
- [🛠 Framework Integration](#-framework-integration)
- [📋 Changelog](#-changelog)
- [🧪 Testing](#-testing)
- [🔒 Security](#-security)
- [🤝 Contributors](#-contributors)
- [📄 License](#-license)

---

## 📖 Overview

The `PhpPy` package provides seamless integration between PHP and Python without API, enabling secure and efficient execution of Python scripts within PHP applications. It ensures structured script execution while managing configurations, arguments, environment variables, and error handling.

---

## Requirements

- PHP 8.1+
- [python3](https://www.python.org/) must be installed in server .

---

## Installation 🛠️

You can install the package via Composer:

```bash
composer require omaralalwi/php-py
```

---

## 🚀 Quick Start

1. 📂 Create a folder for scripts, e.g., `phpPyScripts` in your project root directory.
2. 📝 Create a Python script file (`.py` extension) and write Python code. [See this script examples](https://github.com/omaralalwi/php-py/tree/master/example-scripts).
3. 🔧 make script file executable, `chmod +x script_file_path` .

### ⚡ Easy Usage

```php
<?php
require_once 'vendor/autoload.php';

use Omaralalwi\PhpPy\PhpPy;
use Omaralalwi\PhpPy\Managers\ConfigManager;

$configManager = new ConfigManager([
       'scripts_directory' => 'phpPyScripts',
       'python_executable' => '/usr/bin/python3',
       'max_timeout' => 120,
]);

try {
   $result = PhpPy::build()
       ->setConfig($configManager)
       ->loadScript('sum_calculator.py')
       ->withArguments([10, 20, 30])
       ->run();
    
    print_r($result); // 60.0
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 🔥 Advanced Usage

```php
<?php
require_once 'vendor/autoload.php';

use Omaralalwi\PhpPy\PhpPy;
use Omaralalwi\PhpPy\Managers\ConfigManager;

$configManager = new ConfigManager([
       'scripts_directory' => 'phpPyScripts',
       'python_executable' => '/usr/bin/python3',
       'max_timeout' => 120,
]);

try {

    $result = PhpPy::build()
        ->setConfig($configManager)
        ->loadScript('advanced_example.py')
        ->withArguments([10, 20, 30])
        ->withEnvironment(['FIRST_ENV_VAR' => 'some value', 'SECOND_ENV_VAR' => 'some value'])
        ->timeout(30)
        ->asJson()
        ->run();

    print_r(json_decode($result));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 🌍 Real-World Example

Example: Running **DeepSeek AI** on your server while handling API requests using PHP.

```php
<?php
require_once 'vendor/autoload.php';

use Omaralalwi\PhpPy\PhpPy;
use Omaralalwi\PhpPy\Managers\ConfigManager;

$configManager = new ConfigManager([
       'scripts_directory' => 'deepSeekScripts',
       'python_executable' => '/usr/bin/python3',
       'max_timeout' => 120,
]);

header('Content-Type: application/json');
$valid_tokens = ['USER1' => 'abcd1234', 'USER2' => 'efgh5678'];
$token = $_POST['token'] ?? '';
if (!isset($valid_tokens[$token])) {
    echo json_encode(['error' => 'Invalid token']);
    exit;
}
$prompt = $_POST['prompt'] ?? '';
if (!empty($prompt)) {
    $clean_prompt = escapeshellarg($prompt);
    $response = PhpPy::build()
       ->setConfig($configManager)
       ->loadScript('model_worker.py')
       ->withArguments($clean_prompt)
       ->timeout(30)
       ->asJson()
       ->run();
    echo json_encode(['response' => trim($response)]);
} else {
    echo json_encode(['error' => 'No prompt provided']);
}
```


## ✨ Features

### 🔐 Secure Execution
- **Path Validation** ✅ Ensures scripts are within allowed directories.
- **Argument & Environment Validation** 🔍 Restricts unauthorized input.
- **Timeout Control** ⏳ Prevents long-running scripts.
- **Uses `proc_close` as an alternative to `shell_exec`**.

### 🔧 Flexible Configuration
- Centralized settings via `ConfigManager`.
- Customizable execution parameters.

### 📤 Output Handling
- Supports JSON parsing.
- Captures and reports script errors.

### 🚨 Error Management
- Detailed exception handling for debugging.
- Standardized error reporting.

### 🔌 Extensibility
- Modular execution through `CommandExecutor`.
- Customizable for advanced use cases.

---


## 🛠 Framework Integration

### [Laravel php-py](https://github.com/omaralalwi/laravel-py)

---

## 📋 Changelog

See detailed release notes in [CHANGELOG.md](CHANGELOG.md) 📜

---

## 🧪 Testing

```bash
./vendor/bin/pest
```

---

## 🔒 Security

**Report Vulnerabilities**: Contact [omaralwi2010@gmail.com](mailto:omaralwi2010@gmail.com) 📩

---

## 🤝 Contributors

A huge thank you to these amazing people who have contributed to this project! 🎉💖

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/omaralalwi">
        <img src="https://avatars.githubusercontent.com/u/25439498?v=4" width="60px;" style="border-radius:50%;" alt="Omar AlAlwi"/>
        <br />
        <b>Omar AlAlwi</b>
      </a>
      <br />
      🏆 Creator
    </td>
  </tr>
</table>

**Want to contribute?** Check out the [contributing guidelines](./CONTRIBUTING.md) and submit a pull request! 🚀

---

## 📄 License

This package is open-source software licensed under the [MIT License](LICENSE.md). 📜

