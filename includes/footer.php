  <!-- Global Footer -->
  <footer class="bg-metallic-dark text-white pt-16 pb-8 border-t border-white/10 mt-auto shadow-2xl">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      <div class="grid md:grid-cols-4 gap-10 mb-12">
        
        <!-- Column 1: Brand Info -->
        <div class="md:col-span-2">
          <div class="flex items-center gap-3 mb-5">
            <div class="relative w-[72px] h-[52px] flex items-center justify-center rounded-xl overflow-hidden shadow-md border border-white/20 bg-black flex-shrink-0">
              <img src="https://i.ibb.co/JFR6RtjJ/PHOTO-2026-05-13-22-59-50.jpg" alt="Logo HK Garage" class="w-full h-full object-contain bg-black">
            </div>
            <div class="leading-tight">
              <div class="font-black text-white text-[15px] font-display">HK GARAGE</div>
              <div class="text-[10px] font-semibold text-brand tracking-[0.15em]">SNC · HARSHIT & KARAN</div>
            </div>
          </div>
          <p class="text-sm text-white/60 leading-relaxed max-w-sm mb-6 font-normal">
            La tua officina di fiducia a Costa di Mezzate. Meccanica, diagnosi elettronica e passione per l'auto dal 2010.
          </p>
          <div class="flex items-center gap-3">
            <a href="https://instagram.com" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/5 hover:bg-brand transition-colors flex items-center justify-center text-white" aria-label="Instagram">
              <i class="fa-brands fa-instagram text-sm"></i>
            </a>
            <a href="https://facebook.com" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/5 hover:bg-brand transition-colors flex items-center justify-center text-white" aria-label="Facebook">
              <i class="fa-brands fa-facebook-f text-sm"></i>
            </a>
            <a href="https://wa.me/390351234567" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/5 hover:bg-brand transition-colors flex items-center justify-center text-white" aria-label="WhatsApp">
              <i class="fa-brands fa-whatsapp text-sm"></i>
            </a>
          </div>
        </div>

        <!-- Column 2: Navigation -->
        <div>
          <div class="text-xs font-black tracking-[0.2em] uppercase text-white/50 mb-4 font-display">NAVIGAZIONE</div>
          <ul class="space-y-2.5 text-sm">
            <li><a href="index.php#home" class="text-white/70 hover:text-white transition-colors">Home</a></li>
            <li><a href="index.php#servizi" class="text-white/70 hover:text-white transition-colors">Servizi</a></li>
            <li><a href="index.php#chi-siamo" class="text-white/70 hover:text-white transition-colors">Chi Siamo</a></li>
            <li><a href="index.php#galleria" class="text-white/70 hover:text-white transition-colors">Galleria</a></li>
            <li><a href="index.php#recensioni" class="text-white/70 hover:text-white transition-colors">Recensioni</a></li>
            <li><a href="news.php" class="text-brand font-bold hover:text-white transition-colors">News</a></li>
            <li><a href="contact.php" class="text-white/70 hover:text-white transition-colors">Contatti</a></li>
          </ul>
        </div>

        <!-- Column 3: Contact Info -->
        <div>
          <div class="text-xs font-black tracking-[0.2em] uppercase text-white/50 mb-4 font-display">CONTATTI & ORARI</div>
          <ul class="space-y-2.5 text-sm text-white/70 font-normal">
            <li><i class="fa-solid fa-phone text-brand mr-2 text-xs"></i> +39 035 123 4567</li>
            <li><i class="fa-solid fa-envelope text-brand mr-2 text-xs"></i> appointments@hkgarage.it</li>
            <li class="leading-relaxed"><i class="fa-solid fa-location-dot text-brand mr-2 text-xs"></i> Via Consortile della Conta, 3<br>24060 Costa di Mezzate (BG)</li>
            <li class="pt-2 text-xs text-white/50"><i class="fa-solid fa-clock text-brand mr-1"></i> Lun - Ven 08:00 - 18:30</li>
          </ul>
        </div>

      </div>

      <!-- Bottom Credits -->
      <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-xs text-white/40">
          © <?php echo date('Y'); ?> HK Garage SNC di Harshit & Karan · P.IVA IT01234567890 · Tutti i diritti riservati
        </div>
        <div class="flex items-center gap-6 text-xs text-white/40">
          <a href="admin/login.php" class="hover:text-brand transition-colors font-bold"><i class="fa-solid fa-user-lock mr-1"></i> Area Admin</a>
          <span>·</span>
          <span>Privacy Policy</span>
          <span>·</span>
          <span>Termini di Servizio</span>
        </div>
      </div>
    </div>
  </footer>

  <!-- JavaScript Interactivity -->
  <script>
    // 1. Mobile Menu Drawer Toggle
    const menuToggle = document.getElementById('menuToggle');
    const navMobile = document.getElementById('navMobile');
    const menuIcon = document.getElementById('menuIcon');

    if (menuToggle && navMobile) {
      menuToggle.addEventListener('click', () => {
        const isOpen = !navMobile.classList.contains('hidden');
        if (isOpen) {
          navMobile.classList.add('hidden');
          menuIcon.className = 'fa-solid fa-bars text-xl';
        } else {
          navMobile.classList.remove('hidden');
          menuIcon.className = 'fa-solid fa-xmark text-xl';
        }
      });

      navMobile.querySelectorAll('a, button').forEach(el => {
        el.addEventListener('click', () => {
          navMobile.classList.add('hidden');
          menuIcon.className = 'fa-solid fa-bars text-xl';
        });
      });
    }

    // 2. Lightbox Gallery Modal
    function openLightbox(url, title) {
      const modal = document.getElementById('lightboxModal');
      const img = document.getElementById('lightboxImg');
      const caption = document.getElementById('lightboxCaption');
      if (modal && img) {
        img.src = url;
        if (caption) caption.textContent = title || '';
        modal.classList.remove('hidden');
      }
    }

    function closeLightbox() {
      const modal = document.getElementById('lightboxModal');
      if (modal) modal.classList.add('hidden');
    }

    // 3. Interactive Booking Modal Logic
    let flatpickrInstance = null;

    function openBookingModal(preselectedService) {
      const modal = document.getElementById('bookingModal');
      if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        document.documentElement.classList.add('overflow-hidden');

        if (preselectedService) {
          const select = document.getElementById('modalServiceSelect');
          if (select) {
            for (let i = 0; i < select.options.length; i++) {
              if (select.options[i].text.toLowerCase().includes(preselectedService.toLowerCase())) {
                select.selectedIndex = i;
                break;
              }
            }
          }
        }
      }
    }

    function closeBookingModal() {
      const modal = document.getElementById('bookingModal');
      if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.documentElement.classList.remove('overflow-hidden');
      }
    }
  </script>

  <!-- Admin-Themed Instant Thank You Confirmation Modal -->
  <div id="thankYouModal" class="fixed inset-0 z-[130] bg-black/65 backdrop-blur-md flex items-center justify-center p-4 lg:p-6 hidden animate-fade-in" onclick="closeThankYouModal()">
    <div class="bg-[#ECECEC] max-w-md w-full rounded-[28px] overflow-hidden shadow-2xl border border-black/10 relative text-ink font-sans p-5 sm:p-6" onclick="event.stopPropagation()">
      
      <!-- Obsidian Hero Banner Header -->
      <div class="bg-[#18181B] text-white p-5 rounded-2xl relative overflow-hidden shadow-md mb-4">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-500/30">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-white text-[11px] font-bold font-display uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            In Attesa (Pending)
          </span>
        </div>
        <h3 class="font-display font-black text-xl text-white leading-tight">
          Richiesta di Prenotazione Inviata!
        </h3>
        <p class="text-xs text-white/70 mt-1">
          Grazie per aver prenotato con <strong class="text-white">HK Garage</strong>
        </p>
      </div>

      <!-- Booking Details Card (Floating White Card) -->
      <div class="bg-white rounded-2xl p-4 border border-black/5 shadow-sm space-y-2.5 mb-4 text-xs font-medium text-ink">
        <div class="flex justify-between items-center pb-2 border-b border-black/5">
          <span class="text-ink/60 font-semibold">Cliente:</span>
          <span id="tyCustomerName" class="font-bold text-ink text-right"></span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b border-black/5">
          <span class="text-ink/60 font-semibold">Email:</span>
          <span id="tyCustomerEmail" class="font-bold text-ink text-right"></span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b border-black/5">
          <span class="text-ink/60 font-semibold">Servizio:</span>
          <span id="tyServiceName" class="font-bold text-brand text-right"></span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b border-black/5">
          <span class="text-ink/60 font-semibold">Data & Orario:</span>
          <span id="tyDateTime" class="font-bold text-ink text-right"></span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-ink/60 font-semibold">Veicolo:</span>
          <span id="tyVehicle" class="inline-block px-2.5 py-0.5 rounded-md bg-[#18181B] text-white font-mono font-bold text-[11px]"></span>
        </div>
      </div>

      <!-- Notification Helper Box -->
      <div class="bg-white/80 rounded-2xl p-3.5 border border-black/5 text-xs text-ink/70 leading-relaxed mb-5 flex items-start gap-2.5">
        <i class="fa-solid fa-envelope-circle-check text-brand text-base flex-shrink-0 mt-0.5"></i>
        <div>
          Riceverai a breve un'email di conferma definitiva dal nostro team all'indirizzo indicato.
        </div>
      </div>

      <!-- Action Button -->
      <button type="button" onclick="closeThankYouModal()" class="w-full h-12 bg-[#18181B] hover:bg-black text-white font-bold rounded-full text-sm shadow-md transition-all hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 font-display uppercase tracking-wider">
        <span>Perfetto, Grazie!</span>
        <i class="fa-solid fa-arrow-right text-xs"></i>
      </button>

    </div>
  </div>

  <script>
    function showThankYouModal(details) {
      const nameElem = document.getElementById('tyCustomerName');
      const emailElem = document.getElementById('tyCustomerEmail');
      const serviceElem = document.getElementById('tyServiceName');
      const dtElem = document.getElementById('tyDateTime');
      const vehicleElem = document.getElementById('tyVehicle');

      if (nameElem) nameElem.textContent = details.customerName || '';
      if (emailElem) emailElem.textContent = details.customerEmail || '';
      if (serviceElem) serviceElem.textContent = details.serviceName || '';
      if (dtElem) dtElem.textContent = (details.date || '') + ' @ ' + (details.time || '');
      if (vehicleElem) vehicleElem.textContent = details.vehicle || '';

      const modal = document.getElementById('thankYouModal');
      if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      }
    }

    function closeThankYouModal() {
      const modal = document.getElementById('thankYouModal');
      if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
      if (window.location.pathname.includes('booking.php')) {
        window.location.href = 'index.php';
      }
    }
  </script>

  <script>
    // ── 5-Minute Cookie & LocalStorage Slot Availability Cache ───────────────
    const SLOTS_CACHE_TTL_MS = 5 * 60 * 1000; // 5 Minutes (300,000 ms)

    function setSlotsCacheCookie(dateStr, data) {
      const cachePayload = {
        timestamp: Date.now(),
        data: data
      };
      const jsonStr = JSON.stringify(cachePayload);
      const cookieName = `hk_slots_${dateStr}`;
      const maxAgeSeconds = 300; // 5 minutes
      document.cookie = `${cookieName}=${encodeURIComponent(jsonStr)}; path=/; max-age=${maxAgeSeconds}; SameSite=Lax`;
      try {
        localStorage.setItem(cookieName, jsonStr);
      } catch (e) {}
    }

    function getSlotsCacheCookie(dateStr) {
      const cookieName = `hk_slots_${dateStr}`;
      
      const nameEQ = cookieName + "=";
      const ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) {
          try {
            const payload = JSON.parse(decodeURIComponent(c.substring(nameEQ.length)));
            if (payload && payload.timestamp && (Date.now() - payload.timestamp < SLOTS_CACHE_TTL_MS)) {
              return payload.data;
            }
          } catch (e) {}
        }
      }

      try {
        const localData = localStorage.getItem(cookieName);
        if (localData) {
          const payload = JSON.parse(localData);
          if (payload && payload.timestamp && (Date.now() - payload.timestamp < SLOTS_CACHE_TTL_MS)) {
            return payload.data;
          } else {
            localStorage.removeItem(cookieName);
          }
        }
      } catch (e) {}

      return null;
    }

    function clearSlotsCacheCookie(dateStr) {
      if (!dateStr) return;
      const cookieName = `hk_slots_${dateStr}`;
      document.cookie = `${cookieName}=; path=/; max-age=0`;
      try {
        localStorage.removeItem(cookieName);
      } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', () => {
      // Initialize Flatpickr datepicker
      const dateInput = document.getElementById('modalBookingDate');
      const timeSelect = document.getElementById('modalBookingTime');
      const serviceSelect = document.getElementById('modalServiceSelect');

      if (dateInput) {
        flatpickrInstance = flatpickr(dateInput, {
          locale: 'it',
          minDate: 'today',
          dateFormat: 'Y-m-d',
          disable: [
            function(date) {
              return (date.getDay() === 0);
            }
          ],
          onChange: function(selectedDates, dateStr) {
            fetchSlotsForDate(dateStr);
          }
        });
      }

      function renderSlotsToSelect(data) {
        if (!timeSelect) return;
        timeSelect.innerHTML = '';
        if (!data.success || data.is_closed) {
          timeSelect.innerHTML = '<option value="">Officina chiusa in questa data</option>';
          return;
        }

        const available = data.slots.filter(s => s.available);
        if (available.length === 0) {
          timeSelect.innerHTML = '<option value="">Nessun orario disponibile per la data</option>';
          return;
        }

        timeSelect.innerHTML = '<option value="">Seleziona orario</option>';
        available.forEach(slot => {
          const opt = document.createElement('option');
          opt.value = slot.time;
          opt.textContent = slot.time;
          timeSelect.appendChild(opt);
        });
      }

      function fetchSlotsForDate(dateStr, forceBypassCache = false) {
        if (!timeSelect || !dateStr) return;
        timeSelect.innerHTML = '<option value="">Caricamento orari...</option>';
        const serviceId = serviceSelect ? serviceSelect.value : 1;

        if (!forceBypassCache) {
          const cachedData = getSlotsCacheCookie(dateStr);
          if (cachedData) {
            renderSlotsToSelect(cachedData);
            return;
          }
        }

        fetch(`api/get-slots.php?date=${dateStr}&service_id=${serviceId}`)
          .then(res => res.json())
          .then(data => {
            if (data && data.success && !data.is_closed) {
              setSlotsCacheCookie(dateStr, data);
            }
            renderSlotsToSelect(data);
          })
          .catch(err => {
            console.error('Error fetching slots:', err);
            timeSelect.innerHTML = '<option value="">Errore caricamento orari</option>';
          });
      }

      const otherGroup = document.getElementById('modalOtherServiceGroup');
      const customInput = document.getElementById('modalCustomService');

      if (serviceSelect) {
        serviceSelect.addEventListener('change', () => {
          if (serviceSelect.value === 'other') {
            if (otherGroup) otherGroup.classList.remove('hidden');
            if (customInput) customInput.required = true;
          } else {
            if (otherGroup) otherGroup.classList.add('hidden');
            if (customInput) {
              customInput.required = false;
              customInput.value = '';
            }
          }

          if (dateInput && dateInput.value) {
            fetchSlotsForDate(dateInput.value);
          }
        });
      }

      // Handle Modal AJAX Booking Submission (INSTANT NON-BLOCKING OPTIMISTIC CONFIRMATION)
      const form = document.getElementById('modalBookingForm');
      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();

          const formData = new FormData(form);
          const bookedDate = formData.get('booking_date');
          const serviceSelectElem = document.getElementById('modalServiceSelect');
          const customServiceVal = formData.get('custom_service');

          let selectedServiceName = '';
          if (serviceSelectElem && serviceSelectElem.value === 'other' && customServiceVal) {
            selectedServiceName = 'Altro: ' + customServiceVal;
          } else if (serviceSelectElem && serviceSelectElem.selectedIndex >= 0) {
            selectedServiceName = serviceSelectElem.options[serviceSelectElem.selectedIndex].text;
          }

          const customerDetails = {
            customerName: formData.get('customer_name'),
            customerEmail: formData.get('email'),
            serviceName: selectedServiceName,
            date: formData.get('booking_date'),
            time: formData.get('booking_time'),
            vehicle: (formData.get('vehicle_brand') || '') + ' ' + (formData.get('vehicle_model') || '') + ' (' + (formData.get('vehicle_registration') || '').toUpperCase() + ')'
          };

          // 1. INSTANTLY close booking modal & show admin-themed thank you modal (Zero latency!)
          closeBookingModal();
          showThankYouModal(customerDetails);
          form.reset();

          // 2. Perform backend DB save & PHPMailer emails asynchronously in the background
          fetch('api/book-appointment.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data && data.success) {
              clearSlotsCacheCookie(bookedDate);
            }
          })
          .catch(err => {
            console.error('Background booking submission error:', err);
          });
        });
      }

      // Intersection Observer for Smooth Scroll Reveal Animations
      const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
      if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('active');
              observer.unobserve(entry.target);
            }
          });
        }, {
          threshold: 0.12,
          rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));
      }
    });
  </script>
</body>
</html>

