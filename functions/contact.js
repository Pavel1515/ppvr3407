// Cloudflare Pages Function — обработчик формы заявок.
// Маршрут: POST /contact  (файл functions/contact.js -> адрес /contact)
// Отправляет заявку в Телеграм через бота.
//
// Нужны переменные окружения (Cloudflare Pages → Settings → Variables and Secrets):
//   TELEGRAM_BOT_TOKEN — токен бота от @BotFather
//   TELEGRAM_CHAT_ID   — id чата/канала, куда слать (свой id можно узнать у @userinfobot)

// Убираем переносы строк (защита от инъекции заголовков) и лишние пробелы.
const clean = (v) => (v ?? '').toString().replace(/[\r\n]+/g, ' ').trim();

// Экранируем спецсимволы для parse_mode: HTML в Телеграме.
const esc = (s) =>
  s.replace(/[<>&]/g, (c) => ({ '<': '&lt;', '>': '&gt;', '&': '&amp;' }[c]));

function redirect(request, path) {
  // 303 — правильный редирект после POST (браузер сделает GET).
  return Response.redirect(new URL(path, request.url).toString(), 303);
}

export async function onRequestPost(context) {
  const { request, env } = context;

  let form;
  try {
    form = await request.formData();
  } catch {
    return redirect(request, '/?error=1#contact');
  }

  // Honeypot: настоящие люди это скрытое поле не заполняют, боты — да.
  if (clean(form.get('website')) !== '') {
    return redirect(request, '/thanks.htm');
  }

  const name = clean(form.get('name'));
  const email = clean(form.get('email'));
  const phone = clean(form.get('phone'));
  const service = clean(form.get('service'));
  const message = (form.get('message') ?? '').toString().trim();

  // Валидация — та же логика, что в старом contact.php.
  const emailValid = email === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  const invalid =
    name === '' ||
    !emailValid ||
    (email === '' && phone === '') || // нужен хотя бы один способ связи
    message === '';

  if (invalid) {
    return redirect(request, '/?error=1#contact');
  }

  const token = env.TELEGRAM_BOT_TOKEN;
  const chatId = env.TELEGRAM_CHAT_ID;

  const text =
    `🔔 <b>Новая заявка с сайта</b>\n\n` +
    `👤 <b>Имя:</b> ${esc(name)}\n` +
    `✉️ <b>Email:</b> ${esc(email || '—')}\n` +
    `📞 <b>Телефон:</b> ${esc(phone || '—')}\n` +
    `🛠 <b>Тип проекта:</b> ${esc(service || '—')}\n\n` +
    `💬 <b>Сообщение:</b>\n${esc(message)}`;

  let ok = false;
  try {
    const resp = await fetch(
      `https://api.telegram.org/bot${token}/sendMessage`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          chat_id: chatId,
          text,
          parse_mode: 'HTML',
          disable_web_page_preview: true,
        }),
      }
    );
    ok = resp.ok;
  } catch {
    ok = false;
  }

  // Если Телеграм не принял — вернём человека к форме, чтобы заявка не потерялась.
  return redirect(request, ok ? '/thanks.htm' : '/?error=1#contact');
}

// Прямой заход по GET на /contact — просто отправляем на главную.
export async function onRequestGet(context) {
  return Response.redirect(new URL('/', context.request.url).toString(), 302);
}
