# Ready-to-use prompts (Whitepaper Section 20)

## Feature-idea prompt

```
IDEA: [describe the idea]
USER PROBLEM: [what real problem does it solve?]
DESIRED OUTCOME: [what becomes easier?]

Constraint: netuqo must remain radically simple.

Please:
1. Read PRODUCT.md and MANIFESTO.md.
2. Evaluate whether this belongs in netuqo.
3. Identify the simplest solution.
4. Prefer no new screen.
5. Explain what should be removed or hidden if this adds complexity.
6. Do not write code yet.
```

## Simplicity audit prompt

```
Perform a netuqo simplicity audit. Read PRODUCT.md and MANIFESTO.md, then inspect the
current application. Do not modify code. Identify unnecessary UI, duplicate concepts,
unnecessary clicks, configuration that could be automated, and any feature that
conflicts with the manifesto. Rank findings by impact on simplicity.
```
