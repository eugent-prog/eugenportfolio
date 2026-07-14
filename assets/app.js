const contactForm = document.querySelector("[data-contact-form]");

function setFormStatus(message, type = "") {
  const status = document.querySelector("[data-form-status]");

  if (!status) {
    return;
  }

  status.textContent = message;
  status.className = `form-status ${type}`.trim();
}

if (contactForm) {
  contactForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const submitButton = contactForm.querySelector("button[type='submit']");
    const formData = new FormData(contactForm);
    const name = String(formData.get("name") || "").trim();
    const email = String(formData.get("email") || "").trim();
    const subject = String(formData.get("subject") || "").trim();
    const message = String(formData.get("message") || "").trim();

    if (!name || !email || !subject || !message) {
      setFormStatus("Please complete all fields.", "error");
      return;
    }

    submitButton.disabled = true;
    setFormStatus("Sending message...", "");

    try {
      const response = await fetch(contactForm.action, {
        method: "POST",
        body: formData,
        headers: {
          Accept: "application/json",
          "X-Requested-With": "fetch",
        },
      });

      const payload = await response.json();

      if (!response.ok || !payload.ok) {
        throw new Error(payload.message || "Message could not be sent.");
      }

      contactForm.reset();
      setFormStatus(payload.message, "success");
    } catch (error) {
      setFormStatus(error.message || "Message could not be sent.", "error");
    } finally {
      submitButton.disabled = false;
    }
  });
}
