document.addEventListener("DOMContentLoaded", function () {
  // Check if we're on the index page
  const createThreadBtn = document.getElementById("createThreadBtn");
  if (createThreadBtn) {
    createThreadBtn.addEventListener("click", function () {
      window.location.href = "create-thread.html";
    });
  }

  // Check if we're on the create thread page
  const threadForm = document.getElementById("threadForm");
  if (threadForm) {
    // Handle cancel button
    const cancelBtn = document.getElementById("cancelBtn");
    cancelBtn.addEventListener("click", function () {
      window.location.href = "index.html";
    });

    // Handle form submission
    threadForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Get form data
      const title = document.getElementById("threadTitle").value;
      const category = document.getElementById("threadCategory").value;
      const content = document.getElementById("threadContent").value;

      // Validate form data
      if (!title || !category || !content) {
        alert("Please fill in all fields");
        return;
      }

      // Submit form using AJAX
      const formData = new FormData(threadForm);

      fetch("create_thread.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Thread created successfully!");
            window.location.href = "index.html";
          } else {
            alert("Error creating thread: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while creating the thread.");
        });
    });
  }

  // Load threads on index page
  const threadsList = document.getElementById("threads-list");
  if (threadsList) {
    // Load threads from the server
    loadThreads();
  }
});

// Function to load threads from the server
function loadThreads() {
  fetch("display_threads.php")
    .then((response) => response.json())
    .then((data) => {
      const threadsList = document.getElementById("threads-list");

      // Clear existing threads
      threadsList.innerHTML = "";

      if (data.length === 0) {
        threadsList.innerHTML =
          '<p class="no-threads">No threads found. Be the first to create a thread!</p>';
        return;
      }

      // Add each thread to the list
      data.forEach((thread) => {
        const threadElement = document.createElement("div");
        threadElement.className = "thread";
        threadElement.innerHTML = `
                <div class="thread-info">
                    <h3 class="thread-title">${thread.title}</h3>
                    <span class="thread-category">${thread.category}</span>
                    <span class="thread-date">Posted on: ${
                      thread.created_at
                    }</span>
                </div>
                <p class="thread-preview">${thread.content.substring(0, 150)}${
          thread.content.length > 150 ? "..." : ""
        }</p>
                <div class="thread-meta">
                    <span class="thread-author">By: ${thread.author}</span>
                    <div class="thread-stats">
                        <span class="thread-likes" data-thread-id="${
                          thread.id
                        }">
                            <i class="like-icon ${
                              thread.user_liked ? "liked" : ""
                            }">❤</i> ${thread.likes}
                        </span>
                        <span class="thread-comments" data-thread-id="${
                          thread.id
                        }">💬 ${
          thread.comments
        }</span> <!-- Added data-thread-id -->
                    </div>
                </div>
            `;

        // Add click event to navigate to thread detail
        threadElement
          .querySelector(".thread-title")
          .addEventListener("click", function () {
            window.location.href = `thread.php?id=${thread.id}`;
          });

        // Add like functionality
        const likeButton = threadElement.querySelector(".thread-likes");
        likeButton.addEventListener("click", function (e) {
          e.stopPropagation(); // Prevent navigation to thread detail
          toggleLike(thread.id);
        });

        threadsList.appendChild(threadElement);
      });
    })
    .catch((error) => {
      console.error("Error:", error);
      const threadsList = document.getElementById("threads-list");
      threadsList.innerHTML =
        '<p class="error">Error loading threads. Please try again later.</p>';
    });
}

// Function to toggle like on a thread
function toggleLike(threadId) {
  // In a real app, you would get the user ID from the session
  const userId = "User";

  fetch("toggle_like.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      thread_id: threadId,
      user_id: userId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update the like count and icon
        const likeElement = document.querySelector(
          `.thread-likes[data-thread-id="${threadId}"]`
        );
        const likeIcon = likeElement.querySelector(".like-icon");

        if (data.liked) {
          likeIcon.classList.add("liked");
        } else {
          likeIcon.classList.remove("liked");
        }

        likeElement.innerHTML = `<i class="like-icon ${
          data.liked ? "liked" : ""
        }">❤</i> ${data.likes}`;
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while processing your like.");
    });
}
