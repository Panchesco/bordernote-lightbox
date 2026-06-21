async function getJsonPost(postId) {
  const formData = new FormData();

  formData.append('action', 'get_json_post');
  formData.append('nonce', BordernoteLightbox.nonce);
  formData.append('post_id', postId);

  const response = await fetch(BordernoteLightbox.ajax_url, {
    method: 'POST',
    body: formData
  });

  return response.json();
}

async function getJsonCategoryPosts(categoryId, limit = 10) {
  const formData = new FormData();

  formData.append('action', 'get_json_category_posts');
  formData.append('nonce', BordernoteLightbox.nonce);
  formData.append('category_id', categoryId);
  formData.append('limit', limit);

  const response = await fetch(BordernoteLightbox.ajax_url, {
    method: 'POST',
    body: formData
  });

  return response.json();
}

function handlePost(postId) {
  getJsonPost(postId).then(console.log)
}

window.addEventListener("DOMContentLoaded", () => {

  const trigs = document.querySelectorAll(".musician-lightbox-trigger");
  trigs.forEach((item) => {
    item.addEventListener("click",(e) => {
      e.preventDefault();
      
      handlePost(item.dataset.id);
      
    });
  });

  const lightboxToggles = document.querySelectorAll(".ml-lightbox-toggle");

  lightboxToggles.forEach((item) => {
    console.log(item)
    item.addEventListener("click",(e) => {
      e.preventDefault();
      document.querySelector("body").classList.toggle("ml-lightbox");
    });
  });

});

function getOrientation(url) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.src = url;
    const imgData = {width:0,height:0,orientation:'square'};

    

    img.onload = () => {
      if (img.naturalWidth > img.naturalHeight) {
        imgData.width = img.naturalWidth;
        imgData.height = img.naturalHeight;
        imgData.orientation = 'landscape';
        resolve(imgData);
      } else if (img.naturalHeight > img.naturalWidth) {
        imgData.width = img.naturalWidth;
        imgData.height = img.naturalHeight;
        imgData.orientation = 'portrait';
        console.log(imgData);
        resolve(imgData);
      } else {
        imgData.width = img.naturalWidth;
        imgData.height = img.naturalHeight;
        imgData.orientation = 'square';
        resolve(imgData);
      }
    };

    img.onerror = reject;
    img.src = url;
  });
}

window.addEventListener("DOMContentLoaded", () => {

               const lightboxToggle = document.querySelectorAll(".lightbox__toggle");
               const lightboxImage = document.querySelector(".lightbox .image-container img");
               const scrollTop = document.querySelectorAll(".scroll-to-top");
               const lightbox = document.querySelector(".lightbox");
               const imageUrl = lightboxImage.getAttribute("src");

               console.log(imageUrl);

               lightboxToggle.forEach( (item) => {
                item.addEventListener("click",(e) => {
                    e.preventDefault();
               // Get the image orienation
              getOrientation(imageUrl)
                .then(imgData => {
                  document.querySelector(".image-container").classList.add(imgData.orientation);
                })
                .then(() => {
                  document.querySelector("body").classList.toggle("lightbox-open");
                  document.querySelector("body").classList.toggle("lightbox-closed");
                });
                })
               })

               scrollTop.forEach( (item) => {
                item.addEventListener("click",(e) => {
                    e.preventDefault();
                    lightbox.scrollTo({
                      top: 0,
                      left: 0,
                      behavior: 'smooth'
                    });
                })
               })

               // Scroll to top visibility
               lightbox.addEventListener("scroll", () => {
                const lightboxHeight = parseInt(document.querySelector(".lightbox__content").scrollHeight);
                const scroll = document.querySelector(".lightbox__content").getBoundingClientRect().top;
                const scrollToTop = document.querySelector('.scroll-to-top');
                if( Math.abs(scroll) >= (lightboxHeight/12) ) {
                    scrollToTop.classList.add('show');
                } else {
                    scrollToTop.classList.remove('show')
                }
               }); 
               
});