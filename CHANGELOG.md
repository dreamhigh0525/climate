## 3.0.0 - 2015-03-01

### Changed

- Custom output writers are added simply via the `output` property on CLImate now, as opposed to the immense amount of scaffolding required before

### Added

- Argument parsing
- StdErr output
- Buffer output
- `animate` method for running ASCII animations in the terminal. Because it's fun.
- Input now bolds the default response if it exists

## 2.6.1 - 2015-01-18

### Fixed

- Added `forceAnsiOn` and `forceAnsiOff` methods to address systems that were not identified correctly

## 2.6.0 - 2015-01-07

### Added

- Allow for passing an array of arrays into `columns` method
- `tab` method, for indenting text
- `padding` method, for padding strings to an equal width with a character
- `League\CLImate\TerminalObject\Repeatable` for repeatable objects such as `tab` and `br`
- `League\CLImate\Decorator\Parser\Ansi` and `League\CLImate\Decorator\Parser\NonAnsi`
- Factories:
    + `League\CLImate\Decorator\Parser\ParserFactory`
    + `League\CLImate\Util\System\SystemFactory`
- Terminal Objects now are appropriately namespaced as `Basic` or `Dynamic`
- Readers and Writers are appropriately namespaced as such under `League\CLImate\Util`

### Fixed

- Labels for `advance` method
- Non-ansi terminals will now have plaintext output instead of jumbled characters
- `border` method now default to full terminal width
