"use client";

import Image from "next/image";
import { useMemo, useState } from "react";

type Product = { id: number; name: string; category: string; price: number; stock: number; image: string; color: string; tag?: string };
type CartLine = Product & { quantity: number };

const products: Product[] = [
  { id: 1, name: "Satin draped blouse", category: "Women", price: 4200, stock: 12, image: "#e9d8cc", color: "Rose", tag: "New" },
  { id: 2, name: "Everyday canvas tote", category: "Accessories", price: 2800, stock: 8, image: "#d9e0db", color: "Sage", tag: "Bestseller" },
  { id: 3, name: "Relaxed linen shirt", category: "Men", price: 5600, stock: 5, image: "#d6dde5", color: "Sky" },
  { id: 4, name: "Soft knit cardigan", category: "Women", price: 6400, stock: 7, image: "#e6e0d2", color: "Oat" },
  { id: 5, name: "Minimal leather wallet", category: "Accessories", price: 3500, stock: 15, image: "#d8c4b3", color: "Tan" },
  { id: 6, name: "Cotton lounge set", category: "Men", price: 7800, stock: 3, image: "#dedce7", color: "Lilac" },
];

const shippingRates = { Bureau: 400, "À domicile": 700 };
const money = (value: number) => `${value.toLocaleString("fr-DZ")} DA`;

function ProductArt({ product }: { product: Product }) {
  return <div className="product-art" style={{ background: product.image }}><span>{product.name.split(" ").slice(0, 2).join(" ")}</span><b>{product.color}</b></div>;
}

export default function Home() {
  const [category, setCategory] = useState("All");
  const [cart, setCart] = useState<CartLine[]>([]);
  const [cartOpen, setCartOpen] = useState(false);
  const [adminOpen, setAdminOpen] = useState(false);
  const [checkoutOpen, setCheckoutOpen] = useState(false);
  const [delivery, setDelivery] = useState<keyof typeof shippingRates>("À domicile");
  const [search, setSearch] = useState("");
  const [notice, setNotice] = useState("");
  const visibleProducts = useMemo(() => products.filter((product) => (category === "All" || product.category === category) && product.name.toLowerCase().includes(search.toLowerCase())), [category, search]);
  const subtotal = cart.reduce((sum, line) => sum + line.price * line.quantity, 0);
  const total = subtotal + (cart.length ? shippingRates[delivery] : 0);
  const itemCount = cart.reduce((sum, line) => sum + line.quantity, 0);

  function addToCart(product: Product) {
    setCart((current) => current.some((line) => line.id === product.id) ? current.map((line) => line.id === product.id ? { ...line, quantity: Math.min(line.quantity + 1, product.stock) } : line) : [...current, { ...product, quantity: 1 }]);
    setNotice(`${product.name} added to your bag`);
    setTimeout(() => setNotice(""), 2200);
  }
  function changeQuantity(id: number, delta: number) { setCart((current) => current.map((line) => line.id === id ? { ...line, quantity: line.quantity + delta } : line).filter((line) => line.quantity > 0)); }

  return (
    <main>
      <div className="announcement">Free delivery to your Bureau from 10,000 DA <span>•</span> COD available everywhere</div>
      <header className="site-header shell">
        <button className="menu-button" aria-label="Open menu">☰</button>
        <button className="brand" onClick={() => { setCategory("All"); setSearch(""); }}><Image src="/logo.jpeg" alt="Lissi" width={43} height={43} /><span>LISSI</span></button>
        <nav><button onClick={() => setCategory("All")}>Shop all</button><button onClick={() => setCategory("Women")}>Women</button><button onClick={() => setCategory("Men")}>Men</button><button onClick={() => setCategory("Accessories")}>Accessories</button></nav>
        <div className="header-actions"><label className="search"><span>⌕</span><input value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Search pieces" aria-label="Search products" /></label><button className="admin-link" onClick={() => setAdminOpen(!adminOpen)}>Admin</button><button className="bag" onClick={() => setCartOpen(true)}>Bag <i>{itemCount}</i></button></div>
      </header>
      <section className="hero shell"><div className="hero-copy"><p className="eyebrow">The everyday edit / 01</p><h1>Good things,<br /><em>beautifully</em> chosen.</h1><p className="hero-text">Thoughtful pieces for the way you live now. Easy to love, made to last.</p><button className="dark-button" onClick={() => document.getElementById("catalog")?.scrollIntoView({ behavior: "smooth" })}>Explore the edit <span>↘</span></button></div><div className="hero-image"><div className="hero-shape">LISSI<br /><small>THE NEW<br />EVERYDAY</small></div><div className="hero-note">Curated in Algeria<br /><strong>Summer / 2026</strong></div></div></section>
      <section className="trust-bar shell"><span>Designed for real life</span><span>Low-key, high quality</span><span>Delivered across Algeria</span><span>Cash on delivery</span></section>
      <section id="catalog" className="catalog shell"><div className="section-heading"><div><p className="eyebrow">Just in</p><h2>Find your everyday.</h2></div><div className="filters">{["All", "Women", "Men", "Accessories"].map((item) => <button key={item} className={category === item ? "active" : ""} onClick={() => setCategory(item)}>{item}</button>)}</div></div><div className="product-grid">{visibleProducts.map((product) => <article className="product-card" key={product.id}><button className="product-visual" onClick={() => addToCart(product)} aria-label={`Add ${product.name} to bag`}><ProductArt product={product} />{product.tag && <span className="tag">{product.tag}</span>}<span className="quick-add">Add to bag +</span></button><div className="product-meta"><div><h3>{product.name}</h3><p>{product.category} · {product.color}</p></div><strong>{money(product.price)}</strong></div><div className="stock"><span className={product.stock < 5 ? "low" : "available"}></span>{product.stock < 5 ? `Only ${product.stock} left` : "In stock"}</div></article>)}</div></section>
      {adminOpen && <aside className="admin-panel"><button className="close" onClick={() => setAdminOpen(false)}>×</button><p className="eyebrow">Store control</p><h2>Inventory</h2><p className="muted">A simple control room for your catalogue.</p>{products.map((product) => <div className="inventory-row" key={product.id}><ProductArt product={product} /><div><b>{product.name}</b><span>{money(product.price)}</span></div><label><span>Stock</span><input type="number" defaultValue={product.stock} min="0" /></label></div>)}<button className="dark-button full" onClick={() => { setNotice("Inventory changes saved"); setAdminOpen(false); }}>Save changes</button></aside>}
      {cartOpen && <div className="overlay" onClick={() => setCartOpen(false)}><aside className="cart-drawer" onClick={(event) => event.stopPropagation()}><div className="drawer-heading"><div><p className="eyebrow">Your selection</p><h2>Your bag <small>({itemCount})</small></h2></div><button className="close" onClick={() => setCartOpen(false)}>×</button></div>{cart.length === 0 ? <div className="empty"><span>♡</span><p>Your bag is waiting for something good.</p><button onClick={() => setCartOpen(false)}>Continue shopping</button></div> : <><div className="cart-lines">{cart.map((line) => <div className="cart-line" key={line.id}><ProductArt product={line} /><div><b>{line.name}</b><span>{money(line.price)}</span><div className="stepper"><button onClick={() => changeQuantity(line.id, -1)}>−</button><span>{line.quantity}</span><button onClick={() => changeQuantity(line.id, 1)}>+</button></div></div></div>)}</div><div className="cart-summary"><div><span>Subtotal</span><strong>{money(subtotal)}</strong></div><div><span>Delivery</span><strong>{money(shippingRates[delivery])}</strong></div><div className="summary-total"><span>Total</span><strong>{money(total)}</strong></div><button className="dark-button full" onClick={() => { setCartOpen(false); setCheckoutOpen(true); }}>Checkout · COD <span>→</span></button><small>Secure checkout · Cash on delivery</small></div></>}</aside></div>}
      {checkoutOpen && <div className="overlay" onClick={() => setCheckoutOpen(false)}><section className="checkout-modal" onClick={(event) => event.stopPropagation()}><button className="close" onClick={() => setCheckoutOpen(false)}>×</button><p className="eyebrow">Almost yours</p><h2>Delivery details</h2><p className="muted">We’ll call to confirm your order before dispatch.</p><div className="checkout-layout"><form onSubmit={(event) => { event.preventDefault(); setCheckoutOpen(false); setCart([]); setNotice("Order received. We will call you shortly."); }}><label>Full name<input required placeholder="Your full name" /></label><label>Phone number<input required type="tel" placeholder="05 00 00 00 00" /></label><div className="form-row"><label>Wilaya<input required placeholder="Algiers" /></label><label>Commune<input required placeholder="Hydra" /></label></div><fieldset><legend>Delivery method</legend><label className="radio"><input type="radio" checked={delivery === "À domicile"} onChange={() => setDelivery("À domicile")} /> À domicile <b>{money(shippingRates["À domicile"])}</b></label><label className="radio"><input type="radio" checked={delivery === "Bureau"} onChange={() => setDelivery("Bureau")} /> Bureau pickup <b>{money(shippingRates.Bureau)}</b></label></fieldset><button className="dark-button full" type="submit">Place COD order <span>→</span></button></form><div className="checkout-total"><p>Order summary</p>{cart.map((line) => <div key={line.id}><span>{line.name} × {line.quantity}</span><b>{money(line.price * line.quantity)}</b></div>)}<hr /><div><span>Delivery</span><b>{money(shippingRates[delivery])}</b></div><div className="summary-total"><span>Total</span><strong>{money(total)}</strong></div></div></div></section></div>}
      {notice && <div className="toast">{notice}</div>}
      <footer className="footer shell"><div><Image src="/logo.jpeg" alt="Lissi" width={32} height={32} /><b>LISSI</b><p>Little things, thoughtfully chosen.</p></div><div><p className="eyebrow">Need help?</p><p>hello@lissi.dz<br />Sat–Thu, 9am–6pm</p></div><div><p className="eyebrow">Follow along</p><p>@lissi.everyday</p></div></footer>
    </main>
  );
}