// Duplicate marquee content for seamless infinite loop (tools section, if present)
const track = document.getElementById('marqueeTrack');
if (track) track.innerHTML += track.innerHTML;

// Reveal on scroll with stagger delay per grid
document.querySelectorAll('.services-grid, .process-grid, .portfolio-grid, .test-grid, .price-grid, .blog-grid').forEach(grid=>{
  Array.from(grid.children).forEach((child,i)=>{ child.style.transitionDelay = (i*90)+'ms'; });
});
const revealEls = document.querySelectorAll('.reveal');
const io = new IntersectionObserver((entries)=>{
  entries.forEach(e=>{
    if(e.isIntersecting){ e.target.classList.add('show'); io.unobserve(e.target); }
  });
}, {threshold:0.12});
revealEls.forEach(el=>io.observe(el));

// Animated stat counters
const statEls = document.querySelectorAll('.stat b[data-count]');
const countIo = new IntersectionObserver((entries)=>{
  entries.forEach(entry=>{
    if(!entry.isIntersecting) return;
    const el = entry.target;
    const target = parseInt(el.dataset.count, 10);
    const suffix = el.dataset.suffix || '';
    const duration = 1200;
    const start = performance.now();
    function tick(now){
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(eased * target) + suffix;
      if(progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
    countIo.unobserve(el);
  });
}, {threshold:0.4});
statEls.forEach(el=>countIo.observe(el));

// FAQ accordion
document.querySelectorAll('.faq-item').forEach(item=>{
  item.querySelector('.faq-q').addEventListener('click', ()=>{
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i=>i.classList.remove('open'));
    if(!wasOpen) item.classList.add('open');
  });
});

// Back to top + navbar shadow + scroll progress
const totop = document.getElementById('totop');
const headerEl = document.querySelector('header');
const progressBar = document.getElementById('scrollProgress');
window.addEventListener('scroll', ()=>{
  if(totop) totop.classList.toggle('show', window.scrollY > 500);
  if(headerEl) headerEl.classList.toggle('scrolled', window.scrollY > 10);
  if(progressBar){
    const doc = document.documentElement;
    const pct = (doc.scrollTop) / (doc.scrollHeight - doc.clientHeight) * 100;
    progressBar.style.width = pct + '%';
  }
});
if(totop) totop.addEventListener('click', ()=> window.scrollTo({top:0, behavior:'smooth'}));


// Tilt effect for cards
function addTilt(selector, intensity){
  document.querySelectorAll(selector).forEach(card=>{
    card.addEventListener('mousemove', (e)=>{
      const r = card.getBoundingClientRect();
      const x = e.clientX - r.left;
      const y = e.clientY - r.top;
      const rotateX = ((y - r.height/2) / (r.height/2)) * -intensity;
      const rotateY = ((x - r.width/2) / (r.width/2)) * intensity;
      card.style.transform = `perspective(700px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
    });
    card.addEventListener('mouseleave', ()=>{ card.style.transform = ''; });
  });
}
if(window.matchMedia('(pointer: fine)').matches){
  addTilt('.service-card', 6);
  addTilt('.portfolio-card', 5);
  addTilt('.price-card', 4);
  addTilt('.blog-card', 4);
}

// Button ripple effect
document.querySelectorAll('.btn').forEach(btn=>{
  btn.addEventListener('click', function(e){
    const rect = this.getBoundingClientRect();
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
    this.appendChild(ripple);
    setTimeout(()=> ripple.remove(), 600);
  });
});

// Mobile burger (simple toggle for nav-links)
const burger = document.getElementById('burger');
const navLinks = document.querySelector('.nav-links');
if(burger && navLinks){
  const closeMobileMenu = () => {
    burger.classList.remove('open');
    navLinks.classList.remove('open');
    document.body.classList.remove('menu-open');
    setTimeout(() => {
      if (!navLinks.classList.contains('open')) {
        navLinks.style.display = 'none';
      }
    }, 360);
  };

  burger.addEventListener('click', () => {
    const isOpen = !navLinks.classList.contains('open');
    burger.classList.toggle('open', isOpen);
    navLinks.classList.toggle('open', isOpen);
    document.body.classList.toggle('menu-open', isOpen);
    if (isOpen) {
      navLinks.style.display = 'flex';
    } else {
      navLinks.style.maxHeight = '0';
      navLinks.style.opacity = '0';
      navLinks.style.transform = 'translateY(-10px)';
    }
  });

  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      if (window.matchMedia('(max-width: 860px)').matches) {
        closeMobileMenu();
      }
    });
  });

  window.addEventListener('resize', () => {
    if (!window.matchMedia('(max-width: 860px)').matches) {
      navLinks.style.display = '';
      navLinks.style.maxHeight = '';
      navLinks.style.opacity = '';
      navLinks.style.transform = '';
      burger.classList.remove('open');
      navLinks.classList.remove('open');
      document.body.classList.remove('menu-open');
    }
  });
}

// Show error banner if the PHP handler redirected back with ?error=1
if(location.search.includes('error=1')){
  const err = document.getElementById('formError');
  const contactSection = document.getElementById('contact');
  if(err){
    err.classList.add('show');
    if(contactSection) contactSection.scrollIntoView({behavior:'smooth'});
  }
}
