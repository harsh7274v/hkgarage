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
      
      // 1. Try reading from Document Cookie
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

      // 2. Fallback: LocalStorage
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

      return null; // Cache miss or expired (> 5 minutes)
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
              return (date.getDay() === 0); // Disable Sundays
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

        // 1. Check 5-minute cookie cache unless forced bypass
        if (!forceBypassCache) {
          const cachedData = getSlotsCacheCookie(dateStr);
          if (cachedData) {
            console.log(`[Cache Hit - 5 Min Cookie] Loaded slot availability for ${dateStr} from cookie cache.`);
            renderSlotsToSelect(cachedData);
            return;
          }
        }

        // 2. Cache miss or expired (> 5 min) -> Fetch fresh from Database API
        console.log(`[Cache Miss / Expired] Fetching fresh availability for ${dateStr} from database API...`);
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

      // Handle Modal AJAX Booking Submission
      const form = document.getElementById('modalBookingForm');
      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();

          const submitBtn = document.getElementById('modalSubmitBtn');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Invio in corso...';
          }

          const formData = new FormData(form);
          const bookedDate = formData.get('booking_date');

          fetch('api/book-appointment.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              // Invalidate 5-minute cookie cache for this date so new lookups get updated slots
              clearSlotsCacheCookie(bookedDate);

              closeBookingModal();
              Swal.fire({
                icon: 'success',
                title: 'Prenotazione Confermata!',
                html: `Grazie <strong>${formData.get('customer_name')}</strong>!<br>La tua prenotazione è stata ricevuta con successo.<br><br>Abbiamo inviato un'email di conferma a <strong>${formData.get('email')}</strong> con tutti i dettagli.`,
                confirmButtonColor: '#E63946',
                confirmButtonText: 'Ottimo, Grazie!'
              });
              form.reset();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Impossibile completare',
                text: data.message || 'Si è verificato un errore durante la prenotazione.',
                confirmButtonColor: '#0B1B2B'
              });
            }
          })
          .catch(err => {
            console.error('Booking submission error:', err);
            Swal.fire({
              icon: 'error',
              title: 'Errore di connessione',
              text: 'Si è verificato un errore di rete. Riprova più tardi.',
              confirmButtonColor: '#0B1B2B'
            });
          })
          .finally(() => {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.innerHTML = '<span>Invia prenotazione</span><i class="fa-solid fa-paper-plane text-xs"></i>';
            }
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

