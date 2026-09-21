
const brandRed = "#d63a33";

function toast(message, type = 'success') {
  const toastEl = document.getElementById("liveToast");
  if (!toastEl || typeof bootstrap === "undefined") return;
  toastEl.querySelector(".toast-body").textContent = message;

  const icon = toastEl.querySelector("#toastIcon");
  if (icon) {
    if (type === 'danger' || type === 'error') {
      icon.className = 'bi bi-exclamation-triangle text-danger me-2';
    } else {
      icon.className = 'bi bi-check-circle text-success me-2';
    }
  }

  bootstrap.Toast.getOrCreateInstance(toastEl).show();
}

function setTheme(theme) {
  document.documentElement.dataset.theme = theme;
  try {
    localStorage.setItem("rfs-theme", theme);
  } catch (e) { }
  document.querySelectorAll("#themeToggle i").forEach((icon) => {
    icon.className = theme === "dark" ? "bi bi-sun" : "bi bi-moon-stars";
  });
}



function initSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("mobileOverlay");
  const toggle = document.getElementById("sidebarToggle");
  const collapse = document.getElementById("sidebarCollapse");
  const close = () => {
    sidebar?.classList.remove("show");
    overlay?.classList.remove("show");
  };
  collapse?.addEventListener("click", () => {
    document.body.classList.toggle("sidebar-collapsed");
    localStorage.setItem("rfs-sidebar", document.body.classList.contains("sidebar-collapsed") ? "collapsed" : "open");
  });
  toggle?.addEventListener("click", () => {
    sidebar?.classList.toggle("show");
    overlay?.classList.toggle("show", sidebar?.classList.contains("show"));
  });
  overlay?.addEventListener("click", close);
  document.querySelectorAll(".sidebar .nav-link").forEach((link) => link.addEventListener("click", close));
  document.body.classList.toggle("sidebar-collapsed", localStorage.getItem("rfs-sidebar") === "collapsed");
}

function initValidation() {
  document.querySelectorAll(".needs-validation").forEach((form) => {
    form.addEventListener("submit", (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        form.classList.add("was-validated");
        form.querySelector(":invalid")?.scrollIntoView({ behavior: "smooth", block: "center" });
        return;
      }
      form.classList.add("was-validated");
    });
    form.querySelectorAll("input, select, textarea").forEach((field) => {
      field.addEventListener("input", () => {
        if (form.classList.contains("was-validated") && field.checkValidity()) {
          field.classList.remove("is-invalid");
          field.classList.add("is-valid");
        }
      });
    });
  });
  document.querySelectorAll(".save-modal").forEach((button) => {
    button.addEventListener("click", () => {
      const form = button.closest(".modal-content")?.querySelector(".needs-validation");
      if (!form) return;
      if (typeof form.requestSubmit === "function") {
        form.requestSubmit();
      } else {
        const submitEvent = new Event("submit", {
          cancelable: true,
          bubbles: true
        });

        form.dispatchEvent(submitEvent);

        if (form.checkValidity()) {
          form.submit();
        }
      }
      if (form.checkValidity()) toast("Changes saved.");
    });
  });
}

function initPasswordToggles() {
  document.querySelectorAll(".password-toggle").forEach((button) => {
    button.addEventListener("click", () => {
      const input = button.parentElement.querySelector(".password-input");
      const isPassword = input.type === "password";
      input.type = isPassword ? "text" : "password";
      button.querySelector("i").className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
    });
  });
}

function initSelect2() {
  if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
    jQuery('.select2').each(function() {
      const $el = jQuery(this);
      const parentModal = $el.closest('.modal');
      $el.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: $el.data('placeholder'),
        dropdownParent: parentModal.length ? parentModal : jQuery(document.body)
      });
    });
  }
}

function initDataTables() {
  if (!window.jQuery || !jQuery.fn.DataTable) return;
  jQuery(".data-table").each(function() {
    // Remove Bootstrap's table-responsive wrapper class which breaks DataTables responsive calculations
    jQuery(this).closest(".table-responsive").removeClass("table-responsive");
    // Add nowrap so columns don't aggressively squish before triggering the + icon
    jQuery(this).addClass("nowrap");
  });

  jQuery(".data-table").each(function() {
    const table = this;
    const headers = Array.from(table.querySelectorAll("thead th"));
    const actionIndex = headers.findIndex((header) => header.textContent.trim().toLowerCase() === "actions");
    const columnDefs = [];

    if (actionIndex !== -1) {
      columnDefs.push({
        targets: actionIndex,
        className: "all",
        responsivePriority: 1,
        orderable: false,
        searchable: false
      });
    }

    if (headers.length > 0) {
      const firstHeaderHasAll = headers[0].classList.contains("all");
      columnDefs.push({ 
        targets: 0, 
        responsivePriority: firstHeaderHasAll ? 1 : 2 
      });
    }

    jQuery(table).DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      language: { search: "Search:" },
      responsive: true,
      columnDefs,
      drawCallback() {
        table.querySelectorAll("tbody tr").forEach((row) => {
          row.addEventListener("click", () => row.classList.toggle("selected"));
        });
        
        table.querySelectorAll(".action-menu").forEach((menu) => {
          menu.closest(".table-responsive, .dt-container")?.classList.add("has-actions");
          const toggle = menu.querySelector('[data-bs-toggle="dropdown"]');
          if (toggle && typeof bootstrap !== "undefined") {
            bootstrap.Dropdown.getOrCreateInstance(toggle, {
              boundary: "viewport",
              popperConfig(defaultConfig) {
                return {
                  ...defaultConfig,
                  strategy: "fixed",
                  modifiers: [
                    ...(defaultConfig.modifiers || []),
                    { name: "preventOverflow", options: { boundary: "viewport", padding: 8 } },
                    { name: "flip", options: { boundary: "viewport", padding: 8 } }
                  ]
                };
              }
            });
          }
        });
      }
    });
  });

  // Adjust DataTables responsive layout when Bootstrap tabs or modals are shown
  jQuery(document).on('shown.bs.tab shown.bs.modal', 'button[data-bs-toggle="tab"], a[data-bs-toggle="tab"], .modal', function() {
    if (window.jQuery && jQuery.fn.DataTable) {
      jQuery('.data-table').DataTable().columns.adjust().responsive.recalc();
    }
  });

  // Fix dropdown clipping in DataTables wrappers
  jQuery(document).on('show.bs.dropdown', '.table-responsive, .dt-container, .dt-layout-row', function() {
    jQuery(this).css('overflow', 'visible');
  }).on('hide.bs.dropdown', '.table-responsive, .dt-container, .dt-layout-row', function() {
    jQuery(this).css('overflow', '');
  });
}

function initCharts() {
  if (typeof Chart === "undefined") return;
  const text = getComputedStyle(document.documentElement).getPropertyValue("--muted").trim();
  const line = getComputedStyle(document.documentElement).getPropertyValue("--line").trim();
  const base = {
    responsive: true,
    plugins: { legend: { labels: { color: text, font: { family: "Poppins" } } } },
    scales: { x: { grid: { display: false }, ticks: { color: text } }, y: { grid: { color: line }, ticks: { color: text } } }
  };
  const make = (id, config) => {
    const el = document.getElementById(id);
    if (el) new Chart(el, config);
  };
  make("attendanceChart", { type: "line", data: { labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"], datasets: [{ label: "Attendance %", data: [88, 91, 90, 93, 92, 95], borderColor: brandRed, backgroundColor: "rgba(214,58,51,.14)", fill: true, tension: .35 }] }, options: base });
  make("creditsChart", { type: "doughnut", data: { labels: ["Consumed", "Remaining", "Expiring"], datasets: [{ data: [38420, 21785, 318], backgroundColor: [brandRed, "#1f2937", "#f59e0b"] }] }, options: { responsive: true } });
  const d = window.reportsData || {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    attendanceData: [88, 91, 90, 93, 92, 95],
    enrollmentData: [86, 102, 118, 96, 134, 151],
    creditData: [60205, 38420, 318],
    revenueData: [420, 465, 510, 490, 575, 630],
    teacherRatingLabels: ["Sarah", "Nina", "Arjun", "Kevin", "Priya"],
    teacherRatingData: [4.9, 4.8, 4.7, 4.6, 4.5],
    teacherAttendanceData: [88, 8, 4],
    inactiveStudentsLabels: ["Guitar", "Ukulele"],
    inactiveStudentsData: [4, 3]
  };

  make("attendanceAnalyticsChart", { type: "line", data: { labels: d.labels, datasets: [{ label: "Attendance", data: d.attendanceData, borderColor: brandRed, backgroundColor: "rgba(214,58,51,.14)", fill: true }] }, options: base });
  make("enrollmentAnalyticsChart", { type: "bar", data: { labels: d.labels, datasets: [{ label: "Enrollments", data: d.enrollmentData, backgroundColor: brandRed }] }, options: base });
  make("creditAnalyticsChart", { type: "bar", data: { labels: ["Allocated", "Consumed", "Expiring"], datasets: [{ label: "Credits", data: d.creditData, backgroundColor: brandRed }] }, options: base });
  make("revenueAnalyticsChart", { type: "line", data: { labels: d.labels, datasets: [{ label: "Revenue", data: d.revenueData, borderColor: brandRed, backgroundColor: "rgba(214,58,51,.14)", fill: true }] }, options: base });
  /* Teacher Ratings */
  make("teacherRatingChart", {
    type: "bar",
    data: {
      labels: d.teacherRatingLabels,
      datasets: [{
        label: "Rating",
        data: d.teacherRatingData,
        backgroundColor: brandRed
      }]
    },
    options: {
      ...base,
      scales: {
        y: {
          min: 0,
          max: 5
        }
      }
    }
  });

  /* Student Attendance */
  make("studentAttendanceChart", {
    type: "line",
    data: {
      labels: d.labels,
      datasets: [{
        label: "Student Attendance %",
        data: d.attendanceData,
        borderColor: brandRed,
        backgroundColor: "rgba(214,58,51,.15)",
        fill: true,
        tension: .4
      }]
    },
    options: base
  });

  /* Teacher Attendance */
  make("teacherAttendanceChart", {
    type: "doughnut",
    data: {
      labels: [
        "Present",
        "Late",
        "Absent"
      ],
      datasets: [{
        data: d.teacherAttendanceData,
        backgroundColor: [
          "#22c55e",
          "#f59e0b",
          "#ef4444"
        ]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom"
        }
      }
    }
  });

  /* Inactive Students */
  make("inactiveStudentsChart", {
    type: "bar",
    data: {
      labels: d.inactiveStudentsLabels,
      datasets: [{
        label: "Inactive Students",
        data: d.inactiveStudentsData,
        backgroundColor: [
          "#f59e0b",
          "#fbbf24"
        ],
        borderRadius: 10,
        borderSkipped: false,
        barThickness: 45
      }]
    },
    options: {
      ...base,
      responsive: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: "#111827",
          padding: 10
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: "#6b7280",
            font: {
              size: 13,
              weight: "600"
            }
          }
        },
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            color: "#6b7280"
          },
          grid: {
            color: "rgba(0,0,0,0.05)"
          }
        }
      }
    }
  });
}

function bookingFilterParams(info = {}) {
  const search = document.getElementById("searchFilter")?.value || "";
  const student = document.getElementById("studentFilter")?.value || "";
  const teacher = document.getElementById("teacherFilter")?.value || "";
  const course = document.getElementById("classTypeFilter")?.value || "";
  const availability = document.getElementById("availabilityFilter")?.value || "";
  const params = new URLSearchParams();

  if (info.startStr) params.set("start", info.startStr);
  if (info.endStr) params.set("end", info.endStr);
  if (search) params.set("search", search);
  if (student) params.set("student_id", student);
  if (teacher) params.set("teacher_id", teacher);
  if (course) params.set("course_id", course);
  if (availability) params.set("availability", availability);

  return params;
}

function openBookingModal(date, event) {
  const modal = document.getElementById("bookingModal");
  if (!modal) return;
  const start = event ? new Date(event.start) : new Date(date);
  const props = event?.extendedProps || {};

  const isAggregate = props.is_aggregate;

  if (props.booked && !isAggregate) {
    openClassDetail(event);
    return;
  }

  if (props.limit_reached) {
    toast("You have reached your daily booking limit.");
    return;
  }

  const dateStr = start.toISOString().slice(0, 10);
  document.getElementById("bookingDate").value = dateStr;

  const slotSelect = document.getElementById("bookingSlotId");
  const confirmBtn = document.getElementById("confirmBooking");
  const metaText = document.getElementById("bookingMeta");

  slotSelect.innerHTML = '<option value="">Loading slots...</option>';
  slotSelect.disabled = true;
  confirmBtn.disabled = true;
  metaText.textContent = "Fetching available slots...";

  bootstrap.Modal.getOrCreateInstance(modal).show();

  const params = bookingFilterParams();
  params.set("date", dateStr);

  fetch('/student/bookings/date-slots?' + params.toString(), {
    headers: { "Accept": "application/json" }
  })
    .then(res => res.json())
    .then(data => {
      slotSelect.innerHTML = '<option value="">Select a time slot</option>';
      let hasBookable = false;

      if (!data.slots || data.slots.length === 0) {
        slotSelect.innerHTML = '<option value="">No slots available</option>';
        metaText.textContent = "No available slots match your criteria.";
        return;
      }

      data.slots.forEach(slot => {
        const opt = document.createElement('option');
        opt.value = slot.id;
        opt.textContent = `${slot.time_formatted} (${slot.teacher_name})`;
        if (!slot.bookable) {
          opt.disabled = true;
          opt.textContent += ` - ${slot.unbookable_reason || 'Unavailable'}`;
        } else {
          hasBookable = true;
        }
        slotSelect.appendChild(opt);
      });

      if (data.limit_reached) {
        slotSelect.disabled = true;
        metaText.textContent = "You have reached your daily booking limit.";
        toast("You cannot book any more classes on this date.");
      } else if (hasBookable) {
        slotSelect.disabled = false;
        confirmBtn.disabled = false;
        metaText.textContent = "Select a time slot and confirm.";
      } else {
        slotSelect.disabled = true;
        metaText.textContent = "All slots are fully booked or unavailable.";
      }
    })
    .catch(err => {
      slotSelect.innerHTML = '<option value="">Error loading slots</option>';
      metaText.textContent = "Failed to load slots. Please try again.";
      toast("Error fetching slots.");
    });
}

function openClassDetail(event) {
  const modal = document.getElementById("classDetailModal");
  const body = document.getElementById("classDetailBody");
  if (!modal || !body || !event) return;

  const props = event.extendedProps || {};
  const start = event.startStr ? new Date(event.startStr) : (event.start ? new Date(event.start) : null);
  const end = event.endStr ? new Date(event.endStr) : (event.end ? new Date(event.end) : null);
  const rows = [
    ["Class", props.course || event.title],
    ["Teacher", props.teacher],
    ["Student", props.student],
    ["Starts", start ? start.toLocaleString([], { dateStyle: "medium", timeStyle: "short", timeZone: "Asia/Kolkata" }) : ""],
    ["Ends", end ? end.toLocaleString([], { dateStyle: "medium", timeStyle: "short", timeZone: "Asia/Kolkata" }) : ""],
    ["Mode", props.mode ? props.mode.charAt(0).toUpperCase() + props.mode.slice(1) : ""],
    ["Notes", props.notes],
  ].filter(([, value]) => value !== undefined && value !== null && value !== "");

  const isTeacher = window.location.pathname.includes('/teacher/');
  const isAdmin = document.getElementById("recordModal") !== null;
  const isStudent = !isAdmin && !isTeacher;
  const bookingId = String(event.id || "").replace(/^booking-/, "");
  const canCancel = (isStudent || isTeacher) && props.status === 'confirmed';

  let actionHtml = '';
  if (props.meet_link && canCancel) {
    actionHtml = `
      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-primary flex-grow-1 join-meet-btn" href="/bookings/${bookingId}/join" target="_blank" rel="noopener">Join Google Meet</a>
        <button class="btn btn-outline-danger cancel-student-btn px-4" type="button" data-booking-id="${event.id}">Cancel Class</button>
      </div>
    `;
  } else if (props.meet_link) {
    actionHtml = `<a class="btn btn-primary w-100 mt-3 join-meet-btn" href="/bookings/${bookingId}/join" target="_blank" rel="noopener">Join Google Meet</a>`;
  } else if (canCancel) {
    actionHtml = `<button class="btn btn-outline-danger w-100 mt-3 cancel-student-btn" type="button" data-booking-id="${event.id}">Cancel Class</button>`;
  }

  body.innerHTML = `
    <div class="alert-stack">
      ${rows.map(([label, value]) => `<div class="alert-row d-flex justify-content-between align-items-center"><strong>${label}</strong><span class="text-end ms-3">${value}</span></div>`).join("")}
    </div>
    ${actionHtml}
    ${isAdmin ? `<button class="btn btn-outline-primary w-100 mt-2 edit-admin-btn" type="button">Edit Class</button>` : ""}
  `;

  bootstrap.Modal.getOrCreateInstance(modal).show();

  if (isAdmin) {
    const editBtn = body.querySelector('.edit-admin-btn');
    if (editBtn) {
      editBtn.addEventListener('click', () => {
        bootstrap.Modal.getInstance(modal)?.hide();
        if (typeof openAdminClassEditModal === 'function') {
          openAdminClassEditModal(props);
        }
      });
    }
  }

  if (isStudent || isTeacher) {
    const cancelBtn = body.querySelector('.cancel-student-btn');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', () => {
        const bookingId = cancelBtn.dataset.bookingId;
        const confirmBtn = document.getElementById('confirmCancelClassBtn');
        const reasonInput = document.getElementById('cancelReasonInput');
        if (confirmBtn) {
          confirmBtn.dataset.bookingId = bookingId;
          if (reasonInput) reasonInput.value = ''; // Reset reason

          bootstrap.Modal.getInstance(modal)?.hide();
          bootstrap.Modal.getOrCreateInstance(document.getElementById('cancelConfirmModal')).show();
        }
      });
    }
  }
}

function initBookingCalendar() {
  const el = document.getElementById("bookingCalendar");
  if (!el || typeof FullCalendar === "undefined") return;
  const eventsUrl = el.dataset.eventsUrl;
  const storeUrl = el.dataset.storeUrl;
  const csrf = el.dataset.csrf;
  const mode = el.dataset.calendarMode || "admin";
  const readOnly = mode === "readonly";
  const calendar = new FullCalendar.Calendar(el, {
    timeZone: 'Asia/Kolkata',
    initialDate: new Date(),
    initialView: mode === "student" ? "dayGridMonth" : (window.innerWidth < 768 ? "listWeek" : "timeGridWeek"),
    height: "auto",
    slotMinTime: "12:00:00",
    slotMaxTime: "24:00:00",
    selectable: !readOnly,
    headerToolbar: { left: "prev,next today", center: "title", right: mode === "student" ? "" : "dayGridMonth,timeGridWeek,timeGridDay,listWeek" },
    events(info, successCallback, failureCallback) {
      if (!eventsUrl) {
        successCallback([]);
        return;
      }

      fetch(`${eventsUrl}?${bookingFilterParams(info).toString()}`, {
        headers: { "Accept": "application/json" }
      })
        .then((response) => response.ok ? response.json() : Promise.reject(response))
        .then(successCallback)
        .catch(failureCallback);
    },
    dateClick(info) {
      if (!readOnly && mode === "student") openBookingModal(info.dateStr);
    },
    select(info) {
      if (!readOnly && mode === "student") openBookingModal(info.startStr);
    },
    eventClick(info) {
      if (readOnly) {
        openClassDetail(info.event);
        return;
      }

      if (mode === "student") {
        openBookingModal(info.event.start, info.event);
      } else {
        openClassDetail(info.event);
      }
    },
    eventClassNames(info) {
      if (info.event.extendedProps?.mode === 'break' && info.view.type === 'dayGridMonth') {
        return ['d-none'];
      }
      return [];
    },
    eventContent(info) {
      let title = info.event.title;



      const isTeacher = window.location.pathname.includes('/teacher');
      const isMobileOrList = window.innerWidth < 768 || info.view.type.includes('list');
      const props = info.event.extendedProps || {};
      const meetLink = props.meet_link;
      const isBreak = props.mode === 'break';
      const bookingId = String(info.event.id || "").replace(/^booking-/, "");

      if (isTeacher && isMobileOrList && !isBreak) {
        let actionHtml = '';
        if (meetLink) {
          actionHtml += `<a href="/bookings/${bookingId}/join" target="_blank" rel="noopener" class="btn btn-sm btn-primary py-0 px-2 join-meet-btn me-1" style="font-size: 0.75rem; line-height: 1.5; border-radius: 4px; flex-shrink: 0;" onclick="event.stopPropagation();">Join</a>`;
        } else {
          actionHtml += `<button class="btn btn-sm btn-secondary py-0 px-2 me-1" style="font-size: 0.75rem; line-height: 1.5; border-radius: 4px; flex-shrink: 0;" disabled onclick="event.stopPropagation();">Join</button>`;
        }
        if (props.status === 'confirmed') {
          actionHtml += `<button class="btn btn-sm btn-outline-danger py-0 px-2 cancel-student-btn" data-booking-id="${bookingId}" style="font-size: 0.75rem; line-height: 1.5; border-radius: 4px; flex-shrink: 0;" onclick="event.stopPropagation();">Cancel</button>`;
        }
        return {
          html: `<div class="d-flex align-items-center justify-content-between w-100 py-1" style="min-width: 0;">
                   <span class="calendar-event-title text-truncate me-2">${title}</span>
                   <div class="d-flex align-items-center flex-shrink-0">${actionHtml}</div>
                 </div>`
        };
      }

      return {
        html: `<span class="calendar-event-title">${title}</span>`
      };
    }
  });
  calendar.render();
  const refresh = () => {
    calendar.refetchEvents();
  };
  ["searchFilter", "studentFilter", "teacherFilter", "classTypeFilter", "availabilityFilter", "classSearch"].forEach((id) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener((id === "classSearch" || id === "searchFilter") ? "input" : "change", refresh);
    if (typeof jQuery !== 'undefined') {
      jQuery(el).on('change', refresh);
    }
  });

  document.getElementById("clearFiltersBtn")?.addEventListener("click", () => {
    ["searchFilter", "studentFilter", "teacherFilter", "classTypeFilter", "availabilityFilter", "classSearch"].forEach((id) => {
      const el = document.getElementById(id);
      if (el) {
        el.value = "";
        if (typeof jQuery !== 'undefined' && jQuery(el).hasClass('select2-hidden-accessible')) {
          jQuery(el).val(null).trigger('change');
        }
      }
    });
    refresh();
  });

  document.getElementById("confirmBooking")?.addEventListener("click", () => {
    const form = document.getElementById("bookingForm");
    if (!form || !storeUrl) return;
    form.classList.add("was-validated");
    if (!form.checkValidity()) return;

    const button = document.getElementById("confirmBooking");
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Booking';

    fetch(storeUrl, {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf
      },
      body: JSON.stringify({
        student_id: document.getElementById("bookingStudent")?.value,
        course_id: document.getElementById("bookingClass")?.value,
        class_slot_id: document.getElementById("bookingSlotId")?.value,
        notes: document.getElementById("bookingNotes")?.value || ""
      })
    })
      .then(async (response) => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
          const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
          throw new Error(firstError || data.message || "Booking failed.");
        }
        return data;
      })
      .then((data) => {
        toast(data.message || "Class booked successfully.");
        bootstrap.Modal.getInstance(document.getElementById("bookingModal"))?.hide();
        form.reset();
        form.classList.remove("was-validated");
        calendar.refetchEvents();
      })
      .catch((error) => toast(error.message, 'danger'))
      .finally(() => {
        button.disabled = false;
        button.innerHTML = "Confirm Booking";
      });
  });
}

function initAuthRedirect() {
  document.querySelectorAll(".auth-form").forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      form.classList.add("was-validated");
      if (form.checkValidity()) window.location.href = form.dataset.redirect || "/admin";
    });
  });
}

function initTabSwipes() {
  const tabContents = document.querySelectorAll(".tab-content");
  tabContents.forEach((tabContent) => {
    let touchstartX = 0;
    let touchstartY = 0;
    let touchendX = 0;
    let touchendY = 0;

    tabContent.addEventListener("touchstart", (e) => {
      touchstartX = e.changedTouches[0].screenX;
      touchstartY = e.changedTouches[0].screenY;
    }, { passive: true });

    tabContent.addEventListener("touchend", (e) => {
      touchendX = e.changedTouches[0].screenX;
      touchendY = e.changedTouches[0].screenY;
      
      const diffX = touchendX - touchstartX;
      const diffY = touchendY - touchstartY;

      // Ensure horizontal swipe is dominant and goes beyond threshold (60px)
      if (Math.abs(diffX) > 60 && Math.abs(diffY) < 40) {
        const activePane = tabContent.querySelector(".tab-pane.active");
        if (!activePane) return;
        
        // Don't switch tabs if the swipe started inside a scrollable table or scrollable div
        const isScrollable = (el) => {
          if (!el || el === tabContent) return false;
          const style = window.getComputedStyle(el);
          if (el.scrollWidth > el.clientWidth && (style.overflowX === "auto" || style.overflowX === "scroll")) {
            return true;
          }
          return isScrollable(el.parentElement);
        };
        if (isScrollable(e.target)) {
          return;
        }

        const id = activePane.getAttribute("id");
        const tabList = document.querySelector(`[data-bs-target="#${id}"], [href="#${id}"]`)?.closest(".nav-tabs");
        if (!tabList) return;

        const tabLinks = Array.from(tabList.querySelectorAll('button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]'));
        const activeLink = tabList.querySelector('button[data-bs-toggle="tab"].active, a[data-bs-toggle="tab"].active');
        if (!activeLink) return;

        const currentIndex = tabLinks.indexOf(activeLink);
        let targetLink = null;

        if (diffX < 0) {
          // Swipe left -> Next tab
          if (currentIndex < tabLinks.length - 1) {
            targetLink = tabLinks[currentIndex + 1];
          }
        } else {
          // Swipe right -> Prev tab
          if (currentIndex > 0) {
            targetLink = tabLinks[currentIndex - 1];
          }
        }

        if (targetLink && typeof bootstrap !== "undefined") {
          const tab = bootstrap.Tab.getOrCreateInstance(targetLink);
          tab.show();
        }
      }
    }, { passive: true });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  let savedTheme = "light";

  try {
    savedTheme = localStorage.getItem("rfs-theme") || "light";
  } catch (e) {
    savedTheme = "light";
  }

  setTheme(savedTheme);
  document.getElementById("themeToggle")?.addEventListener("click", () => {
    setTheme(document.documentElement.dataset.theme === "dark" ? "light" : "dark");
  });
  initSidebar();
  initValidation();
  initPasswordToggles();
  initDataTables();
  initSelect2();
  initCharts();
  initBookingCalendar();
  initAuthRedirect();
  initTabSwipes();
  document.querySelectorAll(".toast-action").forEach((el) => el.addEventListener("click", () => toast(el.dataset.toast || "Action completed.")));
});





const availabilityStatus = document.getElementById("availabilityStatus");
const unavailableSection = document.getElementById("unavailableSection");

if (availabilityStatus && unavailableSection) {

  availabilityStatus.addEventListener("change", function () {

    if (this.value === "Unavailable") {
      unavailableSection.classList.remove("d-none");
    } else {
      unavailableSection.classList.add("d-none");
    }

  });

}

function addCourseRow() {
  const container = document.getElementById("courseContainer");

  const div = document.createElement("div");
  div.className = "input-group mb-2 course-row";

  div.innerHTML = `
        <input type="text"
               class="form-control"
               placeholder="Course Name">

        <button class="btn btn-outline-danger"
                type="button"
                onclick="removeCourse(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;

  container.appendChild(div);
}

function removeCourse(btn) {
  btn.closest(".course-row").remove();
}

// -------------------------------------------------------------
// Real-Time Notifications Polling
// -------------------------------------------------------------
let notificationPollInterval = null;

function initNotificationPolling() {
  const bell = document.getElementById('notificationBell');
  if (!bell) return; // User is not logged in or doesn't have the bell (e.g. Student)

  // Poll every 10 seconds
  fetchNotifications();
  notificationPollInterval = setInterval(fetchNotifications, 10000);
}

async function fetchNotifications() {
  try {
    const res = await fetch('/notifications/unread', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (!res.ok) return;

    const data = await res.json();
    updateNotificationUI(data);
  } catch (err) {
    console.error('Failed to fetch notifications:', err);
  }
}

function updateNotificationUI(data) {
  const badge = document.getElementById('notificationBadge');
  const pulse = document.getElementById('notificationPulse');
  const list = document.getElementById('notificationList');

  if (!badge || !pulse || !list) return;

  // Update badge and pulse
  if (data.count > 0) {
    badge.textContent = data.count;
    badge.style.display = 'inline-block';
    pulse.style.display = 'block';
  } else {
    badge.style.display = 'none';
    pulse.style.display = 'none';
  }

  // Update dropdown list
  if (!data.notifications || data.notifications.length === 0) {
    list.innerHTML = '<li><div class="dropdown-item text-center text-muted py-3">No new notifications</div></li>';
    return;
  }

  let html = '';
  data.notifications.forEach(notif => {
    const title = notif.data.title || 'Notification';
    const message = notif.data.message || '';
    const icon = notif.data.icon || 'bi-bell';
    const actionUrl = notif.data.action_url || notif.data.link || '#';
    // Basic relative time parsing for display (could use date-fns/moment if available, fallback to simple string)
    const d = new Date(notif.created_at);
    const timeStr = d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    html += `
      <li>
        <a href="${actionUrl}" onclick="markNotificationRead('${notif.id}')" class="dropdown-item d-flex align-items-start gap-3 py-2 text-start border-0 bg-transparent text-decoration-none">
          <div class="icon-circle bg-primary-subtle text-primary mt-1">
            <i class="bi ${icon}"></i>
          </div>
          <div class="text-wrap">
            <div class="fw-semibold mb-1" style="font-size: 0.9rem;">${title}</div>
            <div class="text-muted small mb-1" style="line-height: 1.3;">${message}</div>
            <div class="text-muted" style="font-size: 0.75rem;">${timeStr}</div>
          </div>
        </a>
      </li>
    `;
  });

  list.innerHTML = html;
}

async function markNotificationRead(id) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrfToken) return;

  try {
    await fetch('/notifications/mark-read', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ id: id })
    });
    fetchNotifications(); // Refresh list immediately
  } catch (err) {
    console.error('Error marking notification as read:', err);
  }
}

async function markAllNotificationsRead() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrfToken) return;

  try {
    await fetch('/notifications/mark-read', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({}) // Empty body for all
    });

    // Optimistic UI update
    document.getElementById('notificationBadge').style.display = 'none';
    document.getElementById('notificationPulse').style.display = 'none';
    document.getElementById('notificationList').innerHTML = '<li><div class="dropdown-item text-center text-muted py-3">No new notifications</div></li>';

  } catch (err) {
    console.error('Error marking all as read:', err);
  }
}

// Global handlers
document.addEventListener('click', function (e) {
  const joinBtn = e.target.closest('.join-meet-btn');
  if (joinBtn) {
    e.preventDefault();
    const url = joinBtn.href;
    const confirmBtn = document.getElementById('joinConfirmBtn');
    if (confirmBtn) {
      confirmBtn.href = url;
      const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('joinConfirmModal'));
      modal.show();
    }
  }

  const cancelBtn = e.target.closest('.cancel-student-btn');
  if (cancelBtn) {
    e.preventDefault();
    const bookingId = cancelBtn.dataset.bookingId;
    const confirmBtn = document.getElementById('confirmCancelClassBtn');
    const reasonInput = document.getElementById('cancelReasonInput');
    if (confirmBtn) {
      confirmBtn.dataset.bookingId = bookingId;
      if (reasonInput) reasonInput.value = ''; // Reset reason

      const classModal = document.getElementById('classDetailModal');
      if (classModal) bootstrap.Modal.getInstance(classModal)?.hide();

      bootstrap.Modal.getOrCreateInstance(document.getElementById('cancelConfirmModal')).show();
    }
  }
});

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
  initNotificationPolling();

  const joinConfirmBtn = document.getElementById('joinConfirmBtn');
  if (joinConfirmBtn) {
    joinConfirmBtn.addEventListener('click', function () {
      const modal = bootstrap.Modal.getInstance(document.getElementById('joinConfirmModal'));
      if (modal) modal.hide();
    });
  }

  const confirmCancelClassBtn = document.getElementById('confirmCancelClassBtn');
  if (confirmCancelClassBtn) {
    confirmCancelClassBtn.addEventListener('click', function () {
      const bookingId = this.dataset.bookingId;
      const reason = document.getElementById('cancelReasonInput')?.value || 'Cancelled';
      const csrf = document.querySelector('meta[name="csrf-token"]').content;

      const originalText = this.innerHTML;
      this.disabled = true;
      this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cancelling...';

      let prefix = '/student';
      if (window.location.pathname.includes('/teacher')) {
        prefix = '/teacher';
      } else if (window.location.pathname.includes('/admin')) {
        prefix = '/admin';
      }

      fetch(`${prefix}/bookings/${bookingId}/cancel`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ reason: reason, notes: reason })
      }).then(res => res.json()).then(data => {
        if (data.message) {
          toast(data.message, 'success');
          bootstrap.Modal.getInstance(document.getElementById('cancelConfirmModal'))?.hide();
          setTimeout(() => window.location.reload(), 1000);
        }
      }).catch(err => {
        toast('Failed to cancel class', 'danger');
      }).finally(() => {
        this.disabled = false;
        this.innerHTML = originalText;
      });
    });
  }
});
