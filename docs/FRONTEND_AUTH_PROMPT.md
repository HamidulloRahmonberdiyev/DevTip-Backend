# Frontend (React) Auth sozlashi – AI/Cursor uchun prompt

Quyidagi matnni frontend loyihangizda Cursor yoki boshqa AI ga yopishtiring (prompt sifatida).

---

## Prompt (nusxalab qo‘ying)

```
Backend Laravel API cookie-based auth ishlatadi. Quyidagilarni qil:

1) API base URL
- Barcha auth so‘rovlar: BASE_URL = import.meta.env.VITE_API_URL yoki "https://devtip.local/api" (productionda o‘zgaradi).
- Har doim credentials yuborilishi kerak (cookie uchun).

2) So‘rovlarda credentials
- fetch ishlatsang: har bir API chaqiruvda options ga qo‘sh: credentials: 'include'
- axios ishlatsang: defaults.withCredentials = true yoki har so‘rovda { withCredentials: true }
- Aksi holda browser cookie yubormaydi va 401 qaytadi.

3) Auth endpoint’lar
- Login (Google): POST BASE_URL/auth/google   Body: JSON { "id_token": "<Firebase/Google id_token>" }   Response 200: { user }
- Joriy user: GET BASE_URL/auth/me   Response 200: { user }   Response 401: { message: "Unauthenticated." } — buni xato deb emas, "foydalanuvchi login qilmagan" deb hisobla
- Logout: POST BASE_URL/auth/logout   Response 200: { message: "Logged out." }

4) 401 (Unauthorized) boshqaruvi
- GET /auth/me 401 — xato emas: foydalanuvchi login qilmagan. State da user = null qilib, login UI ko‘rsat. Console ga error log yozma yoki faqat 401 ni silent qil (catch qilib user ni null qil).
- Boshqa route’larda 401 bo‘lsa (masalan so‘rov davomida session tugasa) — user ni logout qil, login sahifasiga yo‘naltir.

5) Auth flow
- Ilova ishga tushganda: bir marta GET /auth/me (credentials: include bilan). 200 bo‘lsa user ni state ga yoz, 401 bo‘lsa user = null.
- Google login: id_token olish (Firebase yoki Google Sign-In), keyin POST /auth/google { id_token }. 200 va { user } kelgach user ni state ga yoz va session cookie avtomatik saqlanadi.
- Logout: POST /auth/logout (credentials bilan), keyin state da user = null.

6) CORS
- Backend CORS_ORIGINS da frontend origin bor (masalan https://devtip.local). Agar 401 yoki CORS xatosi bo‘lsa, .env da VITE_API_URL to‘g‘ri ekanini va barcha so‘rovlarda credentials: 'include' / withCredentials: true ishlatilayotganini tekshir.
```

---

## Local da 401 bo‘lsa (React localhost:5173, API devtip.local)

Brauzer **localhost:5173** va **devtip.local** ni turli origin deb biladi, shuning uchun cookie yuborilmaydi. **Yechim: Vite proxy** — API so‘rovlarni React dev server orqali backend’ga yo‘naltirish, brauzer uchun bitta origin bo‘ladi.

**React loyihasida (frontend)** `vite.config.ts` yoki `vite.config.js` da:

```js
export default defineConfig({
  server: {
    proxy: {
      '/api': {
        target: 'http://devtip.local',  // yoki Laravel backend manzili
        changeOrigin: true,
      },
    },
  },
});
```

**Frontend .env:**
```env
VITE_API_URL=
```
(yoki bo‘sh qoldiring) — so‘rovlar relative bo‘ladi: `fetch('/api/auth/me', { credentials: 'include' })`. Brauzer so‘rovni localhost:5173 ga yuboradi, Vite uni devtip.local ga proxy qiladi, javob va cookie localhost:5173 ga qaytadi → cookie saqlanadi va keyingi so‘rovda yuboriladi.

**Backend .env (Laravel):**
```env
APP_URL=http://devtip.local
SESSION_SAME_SITE=lax
SESSION_SECURE_COOKIE=
```
(Local HTTP da `lax` + secure=false — proxy orqali same-origin bo‘ladi.)

---

## Qisqa tekshiruv ro‘yxati

- [ ] **Local:** Vite proxy `/api` → backend; frontend da `VITE_API_URL=` (bo‘sh) yoki `''`
- [ ] Barcha API so‘rovlarida `credentials: 'include'` (fetch) yoki `withCredentials: true` (axios)
- [ ] `GET /auth/me` 401 ni xato emas, balki "user yo‘q" deb boshqarish (user = null, login UI)
- [ ] Login: `POST /auth/google` body `{ id_token }`, keyin state’da user saqlash
- [ ] Logout: `POST /auth/logout`, keyin user = null
- [ ] Production: `VITE_API_URL` to‘g‘ri (masalan `https://devtip.local/api`)
