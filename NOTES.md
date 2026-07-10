# PSC Session — Design Notes

## Philosophy

PSC Session is intentionally built as a modern React application embedded inside a PrestaShop module.

The objective is **not** to use the traditional Smarty rendering system whenever it is unnecessary, but instead to expose lightweight React entry points while keeping the module compatible with PrestaShop's lifecycle.

React owns the UI, PHP only provides data.

---

# Main architecture

```
PrestaShop
        │
        ▼
 Module Controllers
        │
        ▼
 Service Container
        │
        ├── ViteAssetManager
        ├── ReviewMailer
        └── ...
        │
        ▼
 React applications
```

The module follows a lightweight dependency injection approach through a custom service container.
Services are instantiated lazily and shared as singletons.

---

# Folder philosophy

The module progressively adopts modern PHP architecture.

Responsibilities are separated into:

- Core
- Infrastructure
- Controllers
- React

rather than placing business logic directly inside controllers.

---

# Design principles

The module follows a few simple rules.

## Controllers

Only coordinate requests.

## Services

Contain technical implementations.

## React

Owns rendering.

## Configuration

Lives outside the code.

## Dependencies

Created through the container.

---

# Why React instead of Smarty

Smarty templates have deliberately been removed.

Reasons:

- React already generates the complete UI.
- No Smarty loops are necessary.
- No Smarty conditions are necessary.
- Only one mounting `<div>` is required.
- The HTML returned by controllers remains minimal.

Instead of : Controller -> Smarty -> React,
the module directly produces : Controller -> React

---

# Infrastructure

## ViteAssetManager

Purpose:

- inject Vite assets
- automatically switch between development and production
- allows react HMR render
- centralize asset loading
- load vite manifest once

## ReviewMailer

Purpose:

- build review emails
- send emails through PHPMailer

It does **not**:

- read HTTP requests
- generate JSON
- know anything about controllers

Responsibilities remain isolated.

## Why PHPMailer?

The module sends its own emails.

Using PrestaShop's Mail class would make the module depend on the shop SMTP configuration.

Instead:

- SMTP settings are completely isolated
- demo shops remain independent
- configuration comes from `.env`

---

# Core

## ServiceContainer

A lightweight dependency injection container.

Responsibilities:

- register factories
- lazily instantiate services
- keep singleton instances

## Why not Symfony Container?

The module only requires a handful of services.
Using Symfony's DI container would introduce unnecessary complexity and coupling.

The custom container is:

- predictable
- easy to debug
- sufficient for the module size

## ContainerFactory

Registers the module's service factories, keeping the main module class free from
dependency registration.

---

# Controllers

## ReviewController

AJAX endpoint.
Receives review submissions from the React application, delegates email delivery to ReviewMailer,
and returns a JSON response indicating whether the operation succeeded or failed.

React form -> ReviewController -> ReviewMailer -> SMTP

Responsibilities:

- receive JSON
- call ReviewMailer
- return JSON

No HTML is generated.

## SessionExpiredController

Standalone React page.
Logs out the current employee, boots the React application by injecting its assets and initial props,
then returns a minimal HTML entry point without relying on Smarty or the PrestaShop layout.

HTTP GET -> Logout employee -> Resolve ViteAssetManager -> Build React Props -> Return React mount point

Responsibilities:

- inject Vite
- expose React props
- render one mounting div

No Smarty template is used.

---

# Environment variables

Sensitive configuration is loaded through the following initialization flow:
bootstrap.php → Composer autoload → Dotenv

---

# Future evolution

Planned improvements:

- MailConfiguration service
- SubmitReview use case
- Review repository
- Review persistence
- additional services registered inside the container

The current architecture has been designed so these additions require minimal modifications.
