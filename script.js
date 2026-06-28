// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// FAQ Accordion - Close others when one is opened
const detailsElements = document.querySelectorAll('details.faq-item');

detailsElements.forEach(targetDetail => {
  targetDetail.addEventListener('click', () => {
    // Close all other details that are not the targetDetail
    detailsElements.forEach(detail => {
      if (detail !== targetDetail) {
        detail.removeAttribute('open');
      }
    });
  });
});
