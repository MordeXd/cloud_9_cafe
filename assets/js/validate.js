$(document).ready(function () {
  
  function validateInput(input) {
    var field = $(input);
    var value = field.val() || "";
    var errorSelector = field.data("error-selector") || "#" + field.attr("name") + "_error";
    var errorfield = $(errorSelector);
    var validationType = field.data("validation") || "";
    var minLength = field.data("min") || 0;
    var maxLength = field.data("max") || 9999;
    var fileSize = field.data("filesize") || 0;
    var fileType = field.data("filetype") || "";
    let errorMessage = "";
    var isFileInput = field.attr("type") === "file";
    var isCheckbox = field.attr("type") === "checkbox";
    var fieldId = field.attr("id");

    if (!validationType) return true;
    
    // SKIP confirmPassword validation - handled directly in register page
    if (fieldId === "confirmPassword") {
      return true;
    }

    // Required field validation
    if (validationType.includes("required")) {
      if (isCheckbox) {
        if (!field.is(":checked")) {
          errorMessage = "You must accept the terms and conditions.";
        }
      } else if (isFileInput) {
        if (!field[0].files || field[0].files.length === 0) {
          errorMessage = "Please select a file to upload.";
        }
      } else if ($.trim(value) === "") {
        errorMessage = "This field is required.";
      }
    }

    // Only continue if no error and has value
    if (!errorMessage && $.trim(value) !== "") {
      
      // Minimum length
      if (validationType.includes("min") && value.length < minLength) {
        // Special message for phone number field
        if (fieldId === "phone" || field.attr("name") === "phone") {
          errorMessage = `Phone number must be ${minLength} digits long.`;
        } else {
          errorMessage = `This field must be at least ${minLength} characters long.`;
        }
      }

      // Maximum length
      if (validationType.includes("max") && value.length > maxLength) {
        errorMessage = `This field must be ${maxLength} characters long.`;
      }

      // Alphabetic
      if (validationType.includes("alphabetic")) {
        var alphabet_regex = /^[a-zA-Z\s]+$/;
        if (!alphabet_regex.test(value)) {
          errorMessage = "Please enter alphabetic characters only.";
        }
      }

      // Email
      if (validationType.includes("email")) {
        var emailRegex = /^[\w-\.]+@([\w-]+\.)+[\w]{2,4}$/;
        if (!emailRegex.test(value)) {
          errorMessage = "Please enter a valid email address.";
        }
      }

      // Number
      if (validationType.includes("number")) {
        var numberRegex = /^[0-9]+$/;
        if (!numberRegex.test(value)) {
          errorMessage = "Please enter only numbers.";
        }
      }

      // Strong password
      if (validationType.includes("strongPassword")) {
        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&])[A-Za-z0-9@$!%*?&]{8,}$/;
        if (!passwordRegex.test(value)) {
          errorMessage = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
        }
      }

      // Select dropdown
      if (validationType.includes("select")) {
        if ($.trim(value) === "" || value === "0") {
          errorMessage = "Please select an option.";
        }
      }
    }

    // File validations
    if (isFileInput && field[0].files && field[0].files.length > 0) {
      var file = field[0].files[0];

      if (validationType.includes("fileSize")) {
        if (file.size > fileSize * 1024) {
          errorMessage = `File size must be less than ${fileSize}KB.`;
        }
      }

      if (validationType.includes("fileType") && !errorMessage) {
        var fileExtension = file.name.split(".").pop().toLowerCase();
        var allowedExtensions = fileType.split(",").map(function(ext) { 
          return ext.trim().toLowerCase(); 
        });
        if (allowedExtensions.indexOf(fileExtension) === -1) {
          errorMessage = `File type must be ${fileType}.`;
        }
      }
    }

    // Show/hide error
    if (errorMessage) {
      errorfield.text(errorMessage).show();
      field.addClass("is-invalid").removeClass("is-valid");
      errorfield.addClass("small text-danger");
      return false;
    } else {
      errorfield.text("").hide();
      field.removeClass("is-invalid").addClass("is-valid");
      return true;
    }
  }
  
  // Input/change validation - SKIP confirmPassword
  $(document).on("input change", "input:not(#confirmPassword), textarea, select", function () {
    validateInput(this);
  });

  // Form submit - SKIP confirmPassword validation
  $(document).on("submit", "form", function (e) {
    var isValid = true;
    var $form = $(this);
    
    $form.find("input:not(#confirmPassword), textarea, select").each(function () {
      if (!validateInput(this)) {
        isValid = false;
      }
    });
    
    if (!isValid) {
      e.preventDefault();
      return false;
    }
  });
});
