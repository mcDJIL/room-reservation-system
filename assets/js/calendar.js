document.addEventListener("DOMContentLoaded", function () {
  console.log("CALENDAR INIT");

  const host = document.querySelector("[data-fc]");
  if (!host) return;

  // 1. INIT CALENDAR TANPA EVENTS DULU
  const calendar = new FullCalendar.Calendar(host, {
    initialView: "dayGridMonth",
    locale: "id",
    height: "auto",
    contentHeight: 600,
    headerToolbar: false,
    displayEventTime: false,
    dayMaxEventRows: 2,

    // penting: kosong dulu biar tidak race condition
    events: [],
  });

  // 2. RENDER DULU
  calendar.render();

  // 3. BARU MASUKKAN DATA EVENT
  calendar.removeAllEvents();
  calendar.addEventSource(window.calendarEvents || []);

  console.log("EVENTS LOADED:", window.calendarEvents);

  // 4. NAVIGATION
  const root = host.closest(".cal-main") || document;

  const monthEl = root.querySelector(".cal-month");

  function updateTitle() {
    const d = calendar.getDate();
    const month = d.toLocaleString("id-ID", { month: "long" });
    const year = d.getFullYear();

    if (monthEl) {
      monthEl.innerHTML = `${month} <span class="yr">${year}</span>`;
    }
  }

  // update awal
  updateTitle();

  // prev / next
  root.querySelectorAll(".cal-nav-btn").forEach((btn, idx) => {
    btn.addEventListener("click", () => {
      idx === 0 ? calendar.prev() : calendar.next();
      updateTitle();
    });
  });

  // today
  const todayBtn = root.querySelector(".cal-today-btn");
  if (todayBtn) {
    todayBtn.addEventListener("click", () => {
      calendar.today();
      updateTitle();
    });
  }

  // view switch
  const VIEW_MAP = {
    Bulan: "dayGridMonth",
    Minggu: "timeGridWeek",
    Hari: "timeGridDay",
    Agenda: "listWeek",
  };

  root.querySelectorAll(".cal-view-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      const label = tab.textContent.trim();
      const view = VIEW_MAP[label] || "dayGridMonth";

      root.querySelectorAll(".cal-view-tab").forEach((t) => {
        t.classList.toggle("is-active", t === tab);
      });

      calendar.changeView(view);
      updateTitle();
    });
  });
});
