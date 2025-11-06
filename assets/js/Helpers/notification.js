const Notification = {
    success(title, text) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            timer: 1500,
            showConfirmButton: false,
            allowOutsideClick: false
        });
    },

    error(title, text) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    },

    confirm(title, text, confirmButtonText = 'Yes, do it!', callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }
};
