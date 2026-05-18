# Safety Model

Structora Core is passive by design.

It may analyze provided documents and structured inputs. It must not submit forms, perform transactions, solve captchas, bypass access controls, or automate user actions.

Public fixtures must be synthetic.
Runtime artifacts must not be committed.
Secrets must never be stored in the repository.

## Rendering Safety

Rendering in Structora Core is passive rendered DOM acquisition. Static rendering normalizes caller-provided HTML. Optional rendered DOM adapters must only acquire a snapshot and return metadata.

Rendering must not click, type, submit forms, execute workflows, solve challenges, bypass authentication, mutate state, or fetch remote inputs through the public CLI.
