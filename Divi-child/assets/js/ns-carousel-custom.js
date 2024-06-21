import { addDotBtnsAndClickHandlers } from "./emblaCarouselDotButton.js";

const OPTIONS = { loop: true };

const emblaNode = document.querySelector(".embla");
const viewportNode = emblaNode.querySelector(".embla__viewport");

const dotsNode = emblaNode.querySelector(".embla__dots");

const emblaApi = EmblaCarousel(viewportNode, OPTIONS);

const removeDotBtnsAndClickHandlers = addDotBtnsAndClickHandlers(
  emblaApi,
  dotsNode
);

emblaApi.on("destroy", removeDotBtnsAndClickHandlers);

window.addEventListener("load", function () {
  var emblaSlides = emblaNode.querySelectorAll(".embla__slide");
  var dotBtns = dotsNode.querySelectorAll(".embla__dot");

  emblaSlides.forEach((slide, index) => {
  
    if (index !== 0) {
      slide.style.opacity = 0.4;
    }
    else {
    slide.style.opacity = 1;
    }
    
    slide.addEventListener("click", function () {
      dotBtns[index].click();
    });
  });
  
  var observer = new MutationObserver((mutationsList) => {
      for (let mutation of mutationsList) {
        if (
          mutation.type === "attributes" &&
          mutation.target.classList.contains("embla__dot--selected")
        ) {
          emblaSlides.forEach((e) => (e.style.opacity = 0.4));
          
          jQuery('.embla__dot').each(function(index){
              if (jQuery(this).hasClass('embla__dot--selected')){
              emblaSlides[index].style.opacity = 1;
              }
          })
        }
      }
    });

    observer.observe(dotsNode, {
      attributes: true,
      childList: true,
      subtree: true,
    });

});