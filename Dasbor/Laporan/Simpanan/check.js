["pokok", "wajib", "sukarela"].forEach(nama => {
  document.getElementById(nama + "aktif").addEventListener("change", function(event) {
    const value = event.target.checked ? 1 : 0;
    const url = new URL(window.location.href);
    url.searchParams.set(nama, value);
    window.location.href = url.toString();
  });
});
