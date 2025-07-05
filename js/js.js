function redirectTo(type) {
    if (type === 'medecin') {
        window.location.href = "inscription_medecin.php";
    } else if (type === 'patient') {
        window.location.href = "inscription_patient.php";
    }
}