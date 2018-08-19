# Advanced Usage

## Information

This area has sections with everything you need to know about the module. This is to ensure your can easily CTRL+F and find
documentation without multiple mouse clicks.

- See Misdirection and it's "Overview" section for an example: [Misdirection](https://github.com/nglasl/silverstripe-misdirection)
- See SteamedClams and everything below "Configuration" section for an example: [Steamed Clams](https://github.com/SilbinaryWolf/silverstripe-steamedclams)

An example of documentation that fragments the documentation is:

- [Silvershop](https://github.com/silvershop/silvershop-core), hard to find out how to disable shipping.
- [Userforms](https://github.com/silvershop/silvershop-core), hard to find information about spam protection support.

## Configuration

Show all configuration values in YML format with their default value

ie.
```yml
SilbinaryWolf\SteamedClams\ClamAV:
  # Make this the same as your clamd.conf settings
  clamd:
    LocalSocket: '/var/run/clamav/clamd.ctl'
  # If true and the ClamAV daemon isn't running or isn't installed the file will be denied as if it has a virus.
  deny_on_failure: false
  # For configuring on existing site builds and ignoring the scanning of pre-module install `File` records. 
  initial_scan_ignore_before_datetime: '1970-12-25 00:00:00'
```