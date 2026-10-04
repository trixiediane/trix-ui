# Docs Preview

This is a minimal static preview for the design system docs. It imports the local core CSS for quick iteration.

Commands:

```bash
# from workspace root
pnpm --filter ./apps/docs install   # optional: installs http-server if using pnpm
pnpm --filter ./apps/docs start
```

Or run the start script with `npx http-server -c-1 . -p 5173` inside `apps/docs`.
