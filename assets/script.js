const ready = (fn) => {
  if (document.readyState !== "loading") {
    fn();
  } else {
    document.addEventListener("DOMContentLoaded", fn, { once: true });
  }
};

ready(() => {
  const navToggle = document.querySelector(".nav-toggle");
  const navLinks = document.querySelector(".nav-links");

  if (navToggle && navLinks) {
    navToggle.addEventListener("click", () => {
      navLinks.classList.toggle("is-open");
    });

    navLinks.querySelectorAll("a[href^='#']").forEach((link) => {
      link.addEventListener("click", () => {
        navLinks.classList.remove("is-open");
      });
    });
  }

  document.addEventListener("click", (event) => {
    const serviceTrigger = event.target.closest("[data-service-id]");
    if (serviceTrigger) {
      const id = serviceTrigger.getAttribute("data-service-id");
      const select = document.querySelector("#service-select");
      if (select) {
        select.value = id;
        document.querySelector("#reserve")?.scrollIntoView({ behavior: "smooth" });
      }
    }

    if (navLinks?.classList.contains("is-open")) {
      const withinNav = event.target.closest(".nav-links") || event.target.closest(".nav-toggle");
      if (!withinNav) {
        navLinks.classList.remove("is-open");
      }
    }
  });

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      rootMargin: "0px 0px -80px 0px",
      threshold: 0.15,
    }
  );

  document.querySelectorAll("[data-animate]").forEach((el) => observer.observe(el));

  const faqItems = document.querySelectorAll(".faq-item");
  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");
    question?.addEventListener("click", () => {
      const isOpen = item.classList.contains("is-open");
      faqItems.forEach((it) => it.classList.remove("is-open"));
      if (!isOpen) {
        item.classList.add("is-open");
      }
    });
  });
});
