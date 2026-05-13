/**
 * FullCalendar wiring for the 2026 design system.
 *
 * A page declares the calendar mount as <div data-fc></div>; this module
 * initializes FullCalendar with the seed events below, themes it via the
 * 2026 toolbar (we hide FC's built-in toolbar and let our own buttons drive
 * .next() / .prev() / .today() / .changeView()), and re-renders on theme
 * change so colors stay in sync.
 */

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

const SEED_EVENTS = [
  { title: 'Rapat tim admin',         start: '2026-05-02T09:00', classNames: ['fc-cat-team'] },
  { title: 'Review peminjaman aula',  start: '2026-05-04T13:00', classNames: ['fc-cat-work'] },
  { title: 'Pemeliharaan Lab Komputer', start: '2026-05-06', allDay: true, classNames: ['fc-cat-work'] },
  { title: 'Koordinasi acara kampus', start: '2026-05-08T10:30', classNames: ['fc-cat-team'] },
  { title: 'Batas persetujuan mingguan', start: '2026-05-10', allDay: true, classNames: ['fc-cat-finance'] },
  { title: 'Audit log reservasi',     start: '2026-05-12T15:00', classNames: ['fc-cat-work'] },
  { title: 'Puncak reservasi bulanan',start: '2026-05-13T09:00', classNames: ['fc-cat-work'] },
  { title: 'Sosialisasi peminjaman',  start: '2026-05-15T11:00', classNames: ['fc-cat-team'] },
  { title: 'Libur nasional',          start: '2026-05-18', allDay: true, classNames: ['fc-cat-personal'] },
  { title: 'Pemutakhiran data ruangan', start: '2026-05-20T14:00', classNames: ['fc-cat-work'] },
  { title: 'Rapat evaluasi',          start: '2026-05-24T09:30', classNames: ['fc-cat-team'] },
  { title: 'Penutupan laporan bulan ini', start: '2026-05-30T16:00', classNames: ['fc-cat-finance'] },
];

const VIEW_MAP = {
  Hari: 'timeGridDay',
  Minggu: 'timeGridWeek',
  Bulan: 'dayGridMonth',
  Agenda: 'listWeek',
  Day: 'timeGridDay',
  Week: 'timeGridWeek',
  Month: 'dayGridMonth',
};

let calendar = null;

function bindToolbar(host) {
  // Wire the page's existing toolbar buttons to the FC instance.
  const root = host.closest('.cal-main') || document;
  const monthEl = root.querySelector('.cal-month');

  const updateTitle = () => {
    if (!monthEl || !calendar) return;
    const d = calendar.getDate();
    const month = d.toLocaleString('id-ID', { month: 'long' });
    const year  = d.getFullYear();
    monthEl.innerHTML = `${month} <span class="yr">${year}</span>`;
  };

  root.querySelectorAll('.cal-nav-btn').forEach((btn, idx) => {
    btn.addEventListener('click', () => {
      if (!calendar) return;
      if (idx === 0) calendar.prev();
      if (idx === 1) calendar.next();
      updateTitle();
    });
  });

  const today = root.querySelector('.cal-today-btn');
  if (today) today.addEventListener('click', () => { calendar.today(); updateTitle(); });

  root.querySelectorAll('.cal-view-tab').forEach((tab) => {
    tab.addEventListener('click', () => {
      const label = tab.textContent.trim();
      const view = VIEW_MAP[label] || 'dayGridMonth';
      root.querySelectorAll('.cal-view-tab').forEach((t) => t.classList.toggle('is-active', t === tab));
      calendar.changeView(view);
      updateTitle();
    });
  });

  // Title isn't filled in until the calendar is rendered.
  setTimeout(updateTitle, 0);
}

function build(host) {
  if (calendar) {
    try { calendar.destroy(); } catch { /* re-build below */ }
  }
  calendar = new Calendar(host, {
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    initialDate: '2026-05-13',
    headerToolbar: false,
    height: '100%',
    locale: 'id',
    expandRows: true,
    dayMaxEvents: 3,
    fixedWeekCount: false,
    firstDay: 0,
    nowIndicator: true,
    selectable: true,
    editable: true,
    events: SEED_EVENTS,
    dayHeaderFormat: { weekday: 'short' },
  });
  calendar.render();
  bindToolbar(host);
}

export function initCalendarPage() {
  const host = document.querySelector('[data-fc]');
  if (!host) return;
  build(host);
  // Re-render on theme change so any token-driven colors update.
  const observer = new MutationObserver((records) => {
    if (records.some((r) => r.attributeName === 'data-theme')) {
      // Just trigger a redraw — colors come from CSS, not from JS.
      if (calendar) calendar.render();
    }
  });
  observer.observe(document.documentElement, { attributes: true });
}
