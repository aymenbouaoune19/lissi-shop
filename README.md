# Lissi

Responsive ecommerce storefront foundation for an Algerian lifestyle shop. The repository contains the original Next.js prototype and an InfinityFree-compatible PHP/MySQL deployment package in `infinityfree/`.

For InfinityFree hosting, use the PHP version. InfinityFree does not run the Node.js server required by Next.js.

## Run locally

```bash
npm install
npm run dev
```

Open `http://localhost:3000`.

## Stack

- Next.js App Router + TypeScript
- CSS responsive layout with a mobile-first breakpoint
- Local product data for the first prototype
- Existing brand asset preserved at `public/logo.jpeg`

## Production roadmap

1. Add PostgreSQL with Prisma for products, variants, stock, orders, wilayas, and delivery rules.
2. Protect `/admin` with authentication and move inventory updates into server actions or API routes.
3. Replace local shipping rates with Anderson's authenticated rate/API integration. Keep a configurable fallback table for outages.
4. Deploy the Next.js app on Vercel, add environment variables for the database and Anderson credentials, then connect a custom domain from Vercel's Domains settings.

## Hosting and domain

Vercel is the simplest fit: import the GitHub repository, use the default Next.js build settings, and deploy. Buy a domain from a registrar such as Namecheap, Cloudflare Registrar, or OVH, then add it in Vercel and follow the displayed DNS records. Use HTTPS provided by Vercel. For production orders, use a managed PostgreSQL provider such as Neon or Supabase and never commit API keys.

## InfinityFree deployment

See [infinityfree/README.md](infinityfree/README.md). Upload the contents of `infinityfree/` to InfinityFree `htdocs`, import `infinityfree/schema.sql` in phpMyAdmin, and configure `config.local.php` with your MySQL credentials. The PHP version includes the catalog, cart, COD checkout, Bureau/home rates, order storage, stock reduction, and admin inventory controls.