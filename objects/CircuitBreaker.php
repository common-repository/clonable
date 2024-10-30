<?php

namespace Clonable\Objects;

class CircuitBreaker {
    private const HALF_OPEN_TIMEOUT = 60;
    private const FAILURE_RATE_THRESHOLD = 5;
    private const SUCCESS_RATE_THRESHOLD = 3;

    private const STATE_OPEN = 0;
    private const STATE_CLOSED = 1;
    private const STATE_HALF_OPEN = 2;

    private const OPTION_SUCCESS_COUNT = 'clonable-circuit-breaker-success-count';
    private const OPTION_FAILURE_COUNT = 'clonable-circuit-breaker-failure-count';
    private const OPTION_OPENED_AT = 'clonable-circuit-breaker-closed-at';

    private $success_count = 0;
    private $fail_count = 0;

    public function __construct() {
        $this->success_count = intval(get_option(self::OPTION_SUCCESS_COUNT, 0));
        $this->fail_count = intval(get_option(self::OPTION_FAILURE_COUNT, 0));
    }

    /**
     * Return true if the circuit breaker is open.
     * @return bool
     */
    public function isOpen() {
        return $this->getState() == self::STATE_OPEN;
    }

    private function getState(): int {
        $opened_at = intval(get_option(self::OPTION_OPENED_AT, 0));

        if ($opened_at == 0) {
            return self::STATE_CLOSED;
        } else if ($opened_at > (time() - self::HALF_OPEN_TIMEOUT)) {
            return self::STATE_OPEN;
        } else {
            return self::STATE_HALF_OPEN;
        }
    }

    /**
     * Handles the circuit breaker logic based on the success parameter.
     * This will handle the different states for the circuit breaker.
     *
     * @param $is_successful bool
     * @return void
     */
    public function handle($is_successful) {
        $state = $this->getState();

        if ($is_successful) {
            // Reset failure count
            $this->fail_count = 0;
            update_option(self::OPTION_FAILURE_COUNT, $this->fail_count, true);


            if ($state == self::STATE_HALF_OPEN) {
                $this->success_count++;
                update_option(self::OPTION_SUCCESS_COUNT, $this->success_count, true);

                // Transition from half open to closed.
                if ($this->success_count >= self::SUCCESS_RATE_THRESHOLD) {
                    update_option(self::OPTION_OPENED_AT, 0, true);
                }
            }
        } else {
            // Reset success count
            $this->success_count = 0;
            update_option(self::OPTION_SUCCESS_COUNT, $this->success_count, true);


            if ($state == self::STATE_CLOSED || $state == self::STATE_HALF_OPEN) {
                $this->fail_count++;
                update_option(self::OPTION_FAILURE_COUNT, $this->fail_count, true);

                // Transition to open
                if ($this->fail_count >= self::FAILURE_RATE_THRESHOLD) {
                    update_option(self::OPTION_OPENED_AT, time(), true);
                }
            }
        }
    }

    /**
     * Prints the debug information for the circuit breaker.
     * @return void
     */
    public function debug() {
        echo "<p style='max-width: 50vw'>The circuit breaker is implemented as a performance measure for the subfolder 
communication between your site and Clonable. The circuit breaker checks for consecutive errors, if more than five 
consecutive occur, the circuit breaker will activate and stop the communication with Clonable to save on performance.
The first lockout will stay in place for 60 seconds. After sixty seconds, Clonable will check for five consecutive
 successful requests. After five successful requests, the circuit breaker will deactivate, and the clone will be reachable.</p>";
        echo "<p>Circuit breaker active: <strong>" . ($this->isOpen() ? 'true' : 'false') . "</strong></p>";
        echo "<p>Consecutive failures: <strong>" . $this->fail_count . "</strong></p>";
        echo "<p>Consecutive successes: <strong>" . $this->success_count . "</strong></p>";
    }
}