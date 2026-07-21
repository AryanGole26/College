</main>

<footer class="border-t border-slate-800/50 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500">&copy; <?php echo date('Y'); ?> City Care Hospital. All rights reserved.</p>
            <p class="text-sm text-slate-600">Built as a college project.</p>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });
</script>
<script>
document.querySelectorAll('.dark-card').forEach(card => {
    card.addEventListener('mousemove', e => {
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--mouse-x', (e.clientX - rect.left) + 'px');
        card.style.setProperty('--mouse-y', (e.clientY - rect.top) + 'px');
    });
});
</script>
</body>
</html>