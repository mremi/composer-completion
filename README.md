Composer-Completion library
===========================

This library allows you to complete the composer command with some packages.

For now, only bash is supported. You need to install `bash-completion`.

**Basic Docs**

* [Installation](#installation)
* [Usage](#usage)
* [Contribution](#contribution)

<a name="installation"></a>

## Installation

Only 4 steps:

### 1. Clone Composer-Completion using Git

```bash
$ git clone git@github.com:mremi/composer-completion.git
$ cd composer-completion
```

### 2. Install the dependencies using Composer

``` bash
$ composer install
```

### 3. Copy bin/bash/composer in /etc/bash_completion.d/composer

```bash
$ sudo cp bin/bash/composer /etc/bash_completion.d/composer
```

Then edit `/etc/bash_completion.d/composer` and replace `__(mremi/composer-completion)__`
by the path where you have installed this library.

### 4. Add the following lines to your ~/.bashrc or ~/.bash_profile:

```bash
if [ -e /etc/bash_completion.d/composer ]; then
    . /etc/bash_completion.d/composer
fi
```

Restart your bash and let's go!

<a name="usage"></a>

## Usage

The require command is completed by calling the Packagist API the first time,
then the results are cached during 1 day.

```bash
composer require mremi[TAB]
```

![Screenshot](https://raw.github.com/mremi/composer-completion/master/doc/images/require.png)

The update command is completed by looking at your composer.json. For instance,
if you have some Symfony dependencies in your project:

```bash
composer update symfony[TAB]
```

![Screenshot](https://raw.github.com/mremi/composer-completion/master/doc/images/update.png)

<a name="contribution"></a>

## Contribution

Any question or feedback? Open an issue and I will try to reply quickly.

A feature is missing here? Feel free to create a pull request to solve it!

I hope this has been useful and has helped you. If so, share it and recommend
it! :)

[@mremitsme](https://twitter.com/mremitsme)
