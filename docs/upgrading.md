# Upgrading

- [From 1.1.1 to 1.2.0](#from-111-to-120)

What changed in each release, and what, if anything, you need to change in your code when upgrading.

## From 1.1.1 to 1.2.0
You don't need to change any code, as long as you're on PHP 8.2 or newer.

- **Fixes output leaking when a view throws.** `render()` used to leave its output buffer open, so a
  half-rendered view appeared in whatever was output next, such as an error page. Its output is now discarded,
  along with any buffers it opened, and the exception is passed on.
- **Keeps the output of a buffer a view forgot to close**, rather than leaving that buffer open.
- **Throws a `LogicException` if a view closes the output buffer it renders to**, which used to return the wrong
  content, or `false`.
- **Requires PHP 8.2 or newer.** Composer keeps projects on PHP 8.0 and 8.1 on version 1.1.1.
- Adds smoke tests on PHP 8.2 to 8.5, PHPStan, a GitHub workflow and this documentation.
