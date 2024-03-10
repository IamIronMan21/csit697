<!doctype html>
<html lang="en" class="overscroll-none">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Quizify</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Literata:ital,opsz@0,7..72;1,7..72&display=swap">

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen font-['Inter']">
  <!-- <nav class="bg-gray-800 px-4">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">

        </div>
        <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
          <div class="flex flex-shrink-0 items-center">
            <h1 class="text-white font-['Literata'] text-xl mr-1">Quizify</h1>
          </div>
          <div class="hidden sm:ml-6 sm:block">
            <div class="flex space-x-4">
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav> -->

  <!-- <div class="mx-auto bg-white min-h-screen px-12 pt-4 w-1/2">
    <a href="start.php">Get started</a>
  </div> -->

  <div class="bg-white">
    <header class="absolute inset-x-0 top-0 z-50 bg-slate-800">
      <nav class="flex items-center justify-center pb-4 pt-8">
        <h1 class="text-white font-['Literata'] text-3xl text-center">Quizify</h1>
      </nav>
    </header>

    <div class="relative isolate px-6 pt-14 lg:px-8 bg-slate-800 text-white">
      <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
        <!-- <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#3d6870] to-[0569b0] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div> -->
      </div>
      <div class="mx-auto max-w-2xl py-20">
        <div class="text-center">
          <h1 class="text-4xl font-medium tracking-tight text-indigo-00 sm:text-4xl font-['Literata']">
            Empowering Tutors for Excellence
          </h1>
          <p class="mt-6 text-lg leading-8 text-gray-200">
            Advance your tutoring with our platform that empowers educators to easily design quizzes.
          </p>
          <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="./start.php" class="rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Get started</a>
            <!-- <a href="#" class="text-sm font-semibold leading-6 text-gray-900">Learn more <span aria-hidden="true">→</span></a> -->
          </div>
        </div>
      </div>
      <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
        <!-- <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div> -->
      </div>
    </div>
  </div>

  <div class="overflow-hidden bg-white py-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
        <div class="lg:pr-8 lg:pt-4">
          <div class="lg:max-w-lg">
            <p class="mt-1 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">A better workflow</p>
            <p class="mt-6 text-lg leading-8 text-gray-600">
              Easily create and share quizzes, and make tutoring enjoyable for both you and your students.
            </p>
            <dl class="mt-10 max-w-xl space-y-8 text-base leading-7 text-gray-600 lg:max-w-none">
              <div class="relative pl-9">
                <dt class="inline font-semibold text-gray-900">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-1 top-1 h-5 w-5 text-indigo-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                  </svg>
                  Easy to use.
                </dt>
                <dd class="inline">Focus solely on managing your courses and quizzes with our intuitive interface.</dd>
              </div>
              <div class="relative pl-9">
                <dt class="inline font-semibold text-gray-900">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-1 top-1 h-5 w-5 text-indigo-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                  </svg>
                  Seamless customization.
                </dt>
                <dd class="inline">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem cupidatat commodo.</dd>
              </div>
              <div class="relative pl-9">
                <dt class="inline font-semibold text-gray-900">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-1 top-1 h-5 w-5 text-indigo-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                  </svg>
                  Responses on-demand.
                </dt>
                <dd class="inline">View responses across all submissions to help students where they need it the most.</dd>
              </div>
            </dl>
          </div>
        </div>
        <img src="./images/screenshot.jpg" alt="Product screenshot" class="w-[48rem] max-w-none rounded-xl shadow-xl ring-1 ring-gray-400/10 sm:w-[57rem] md:-ml-4 lg:-ml-0" width="2432" height="1442">
      </div>
    </div>
  </div>

  <hr>

  <div class="my-24 px-">
    <div class="flex w-full">
      <div class="w-2/5">
        <h2 class="font-bold text-3xl">Frequently asked questions</h2>
        <p class="mt-4 text-slate-500">Can't find the answer you're looking for? Reach out to our <a href="" class="text-indigo-600">support team</a>.</p>
      </div>
      <div class="w-3/5">
        <div>
          <div class="mb-8">
            <dt class="font-semibold">How do I share a quiz to a student for them to take?</dt>
            <dd class="mt-2 text-gray-600 leading-7">
              ...
            </dd>
          </div>
          <div class="mb-8">
            <dt class="font-semibold">Am I able to edit my quizzes?</dt>
            <dd class="mt-2 text-gray-600 leading-7">
              Yes. However, quizzes can only be edited if there is no submissions made on it. Once a quiz has a submission, it can no longer be edited.
            </dd>
          </div>
          <div class="mb-8">
            <dt class="font-semibold">If I can't edit a quiz with a submission, what then can I do?</dt>
            <dd class="mt-2 text-gray-600 leading-7">
              Quizify has a feature that lets you clone a quiz. Cloning a quiz creates a brand new quiz with the same questions of the cloned quiz and can be edited since it's new.
            </dd>
          </div>
          <div class="mb-8">
            <dt class="font-semibold">What happens if I delete a quiz or even a course?</dt>
            <dd class="mt-2 text-gray-600 leading-7">
              Deleting a quiz will delete that quiz itself and any data associated to its questions and its submissions. Deleting a course will delete the course itself and every associated quiz.
            </dd>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>

</html>
