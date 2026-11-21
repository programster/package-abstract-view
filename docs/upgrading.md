# Upgrading

- [From 1.2.0 to 2.0.0](#from-120-to-200)
- [From 1.1.1 to 1.2.0](#from-111-to-120)

What changed in each release, and what, if anything, you need to change in your code when upgrading.

## From 1.2.0 to 2.0.0
`render()` now declares that it returns a `string`, and `renderContent()` that it returns `void`.

**Changes you need to make:**

- Add `: void` to every view's `renderContent()`, or PHP stops with a fatal error when the view's class is loaded:
  `protected function renderContent() : void`. It must not `return` a value.
- If a view overrides `render()`, add `: string` to it.

Also tests the package on PHP 8.6.

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
