<!-- Newsletter Signup Section -->
<section class="bg-brand text-white py-16">
    <div class="container-wide">
        <div class="grid md:grid-cols-2 gap-10 items-center">

            <!-- Left Column: Call to Action -->
            <div>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Stay Updated With Our Latest News
                </h2>
                <p class="text-blue-100 mb-6">
                    Subscribe to our newsletter to receive the latest updates, product launches, and insights directly to your inbox.
                </p>
                <p class="font-medium text-blue-50">
                    Join our community and never miss an update!
                </p>
            </div>

            <!-- Right Column: Form -->
            <form class="bg-slate-800 bg-opacity-30 backdrop-blur-sm rounded-xl p-8 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium mb-2">Name</label>
                    <input type="text" id="name" name="name" required
                        class="w-full rounded-md px-4 py-3 bg-white/10 text-white placeholder-blue-100 focus:outline-none focus:ring-2 focus:ring-white">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="w-full rounded-md px-4 py-3 bg-white/10 text-white placeholder-blue-100 focus:outline-none focus:ring-2 focus:ring-white">
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-4">
                    <button type="button"
                        class="flex-1 border border-white/60 text-white py-3 rounded-md hover:bg-white hover:text-blue-600 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-white text-blue-600 py-3 rounded-md font-semibold hover:bg-blue-50 transition-colors duration-200">
                        Submit
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>
